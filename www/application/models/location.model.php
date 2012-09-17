<?php
// http://www.movable-type.co.uk/scripts/latlong-db.html

class LocationModel
    extends Model
{
    function clear_geocode_cache()
    {
        $query =<<<EOQ
            TRUNCATE TABLE tblCacheGeocode
EOQ;

        $prepare = $this->query($query);
    }

    function getCachedLatLong($query, $force_lookup = 0)
    {
        if (empty($query))
            return false;

        // for each ',', add a space
        $query = preg_replace('/,/', ', ', $query);

        // for each multiple spaces, make it single space
        $query = preg_replace('/\s\s+/', ' ', $query);

        // trim it, make it all lowercase, and make the first letter in each word upper case
        $query = ucwords(strtolower(trim($query)));

        $ret_latlong = array(
            'status' => false,
            'details' => array(),
            'coords' => array(
                'latitude' => 0,
                'longitude' => 0,
            ),
        );

        $status  = &$ret_latlong['status'];
        $details = &$ret_latlong['details'];
        $coords  = &$ret_latlong['coords'];

        $latlong = $force_lookup ? false : $this->getLatLongByCache($details, $query);
        if (!empty($latlong))
        {
            $status = true;
            $coords['latitude']  = $latlong['latitude'];
            $coords['longitude'] = $latlong['longitude'];
            return $ret_latlong;
        }

        $latlong = $this->getLatLongByOpenMapQuest($details, $query);
        if (empty($latlong))
        {
            // couldn't get the lat/long
            return $ret_latlong;
        }
        else
        {
            $status = true;
            $coords['latitude']  = $latlong['latitude'];
            $coords['longitude'] = $latlong['longitude'];
        }

        $lat  = $latlong['latitude'];
        $long = $latlong['longitude'];
        $add_ok = $this->addLatLongToCache($details, $query, $lat, $long);
        if (empty($add_ok))
        {
            // failed to cache new query and lat/long
            Util::logit("Failed to cache lat/long. Lat: $lat, Long: $long, Query: $query");
        }

        return $ret_latlong;
    }

    function getLatLongByCache(&$details, $query_addy)
    {
        $query =<<<EOQ
            SELECT
                latitude, longitude
            FROM tblCacheGeocode
            WHERE address_query = :query
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':query'=>$query_addy), __FILE__, __LINE__);
        if (!$prepare) return false;

        $row_cache = $prepare->fetch(PDO::FETCH_ASSOC);
        unset($prepare);

        if (empty($row_cache))
            return false;

        //Util::logit("Geocoder Cache hit: $query_addy");

        // update the hit counter for the cache hit

        $query =<<<EOQ
            UPDATE tblCacheGeocode
            SET hits = hits + 1
            WHERE address_query = :query
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':query'=>$query_addy), __FILE__, __LINE__);
        if (!$prepare)
        {
            Util::logit("Failed to update hit counter for query: $query_addy");
        }

        return $row_cache;
    }

    function getLatLongByOpenMapQuest(&$details, $query_addy)
    {
        $params = array(
            'inFormat' => 'kvp',
            'outFormat' => 'json',
            'ignoreLatLngInput' => 'true',
            'thumbMaps' => 'false',
            'location' => $query_addy,
        );

        $url  = 'http://open.mapquestapi.com/geocoding/v1/address?';
        $url .= http_build_query($params, '', '&');

        ob_start();
        $get_data = file_get_contents($url);
        $json_error = ob_get_clean();
        if (empty($get_data))
        {
            Util::logit("Failed to get json data from $url. Error: $json_error");
            return false;
        }

        $json_data = json_decode($get_data, true);

        $details['json'] = $json_data;

        // statuscode and message
        $info = $json_data['info'];
        if ($info['statuscode'] != 0)
        {
            Util::logit("[Open Map Quest] Query: $query_addy. Status Code: ${info['statuscode']}. Messages:".print_r($info['messages'],true));
        }

        $results = $json_data['results'];
        if (empty($results))
            return false;

        $locations = $results[0]['locations'];

        $latlng = false;
        foreach ($locations as $loc)
        {
            if ($loc['adminArea1'] === 'United States of America')
            {
                // only get the first US lat/long
                $latlng = array(
                    'latitude'  => $loc['latLng']['lat'],
                    'longitude' => $loc['latLng']['lng'],
                );
                break;
            }
        }

        return $latlng;
    }

    function addLatLongToCache(&$details, $query_addy, $lat, $long)
    {
        $query =<<<EOQ
            INSERT INTO tblCacheGeocode
            SET
                address_query = :query,
                latitude = :latitude,
                longitude = :longitude
            ON DUPLICATE KEY UPDATE
                latitude = :u_latitude,
                longitude = :u_longitude
EOQ;

        $params = array(
            ':query'        => $query_addy,
            ':latitude'     => $lat,
            ':longitude'    => $long,
            ':u_latitude'   => $lat,
            ':u_longitude'  => $long,
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare)
        {
            Util::logit("Failed to add '$query_addy', lat:$lat, long:$long to geocoder cache.");
            return false;
        }

        return true;
    }

//    function getLatLongByZip($zip)
//    {
//        $query =<<<EOQ
//            SELECT
//                latitude, longitude
//            FROM tblLocation_us
//            WHERE `country code` = 'US'
//            AND `postal code` = :zip
//EOQ;
//
//        $prepare = $this->prepareAndExecute($query, array(':zip'=>$zip), __FILE__, __LINE__);
//        if (!$prepare) return false;
//
//        $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
//        $row = array_shift($rows);
//        return $row;
//    }
//
//    function parseCityState($citystate)
//    {
//        $split = explode(',', $citystate);
//        $n_split = count($split);
//        if ($n_split < 2)
//            return false;
//
//        $city = trim($split[$n_split -2]);
//        $state = trim($split[$n_split -1]);
//
//        return array('city'=>$city, 'state'=>$state);
//    }
//
//    function getLatLongByCityState($city, $state)
//    {
//        $query =<<<EOQ
//            SELECT
//                AVG(DISTINCT latitude) AS latitude,
//                AVG(DISTINCT longitude) AS longitude
//            FROM tblLocation_us
//            WHERE `country code` = 'US'
//            AND `place name` = :city
//            AND `state code1` = :state
//EOQ;
//
//        $prepare = $this->prepareAndExecute($query, array(':city'=>$city, ':state'=>$state), __FILE__, __LINE__);
//        if (!$prepare) return false;
//
//        $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
//        $row = array_shift($rows);
//        return $row;
//
//    }
//
//    function getLocationsByAddress($address, $state_filters)
//    {
//        $query_in = implode(',', array_fill(0, count($state_filters), '?'));
//        $query =<<<EOQ
//            SELECT * FROM
//            (
//                SELECT
//                    latitude, longitude,
//                    `postal code` AS zip,
//                    `place name` AS city, `state code1` AS state,
//                    MATCH(`state code1`, `place name`) AGAINST(?) AS score
//                FROM tblLocation_us
//                WHERE MATCH(`state code1`, `place name`) AGAINST(?)
//                AND `state code1` IN ({$query_in})
//            ) tblLocations
//            ORDER BY score DESC, zip
//EOQ;
//
//        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
//        if (!$prepare) return false;
//
//        $rst = $prepare->bindValue(1, $address);
//        $rst = $prepare->bindValue(2, $address);
//
//        foreach ($state_filters as $idx => $filter)
//        {
//            $rst = $prepare->bindValue($idx+3, $filter);
//            if (!$rst) return false;
//        }
//
//        $rst = $prepare->execute();
//        if (!$rst) return false;
//
//        $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
//        return $rows;
//    }

    function getLocationsWithinLatLong($lat, $long, $withinRadius)
    {
        // radius of earth
        // miles - 3959
        // km - 6371

        $radius_earth = 3959;

        // first-cut bounding box (in degrees)
        $maxLat = $lat + rad2deg($withinRadius/$radius_earth);
        $minLat = $lat - rad2deg($withinRadius/$radius_earth);

        // compensate for degrees longitude getting smaller with increasing latitude
        $maxLong = $long + rad2deg($withinRadius/$radius_earth/cos(deg2rad($lat)));
        $minLong = $long - rad2deg($withinRadius/$radius_earth/cos(deg2rad($lat)));

        // convert origin of filter circle to radians
        $lat = deg2rad($lat);
        $long = deg2rad($long);

        $query_bounds =<<<EOQ
            SELECT
                latitude, longitude,
                `postal code`,
                `place name` AS city,
                `state name1`, `state code1`,
                accuracy
            FROM tblLocation_us
            WHERE latitude > {$minLat} AND latitude < {$maxLat}
            AND longitude > {$minLong} AND longitude < {$maxLong}
EOQ;

        $query_distance =<<<EOQ
            SELECT
                *,
                ACOS(
                    SIN({$lat})*SIN(RADIANS(latitude)) +
                    COS({$lat})*COS(RADIANS(latitude)) * COS(RADIANS(longitude) - {$long})
                ) * {$radius_earth}  AS distance
            FROM
            (
                {$query_bounds}
            ) AS tblLatLongBounds
EOQ;

        $query =<<<EOQ
            SELECT
                latitude, longitude,
                `postal code` AS zip,
                city, `state code1` AS state,
                distance
            FROM
            (
                {$query_distance}
            ) AS tblDistance
            WHERE distance < :withinRadius
            ORDER BY distance ASC
EOQ;

        $opts = array(
            ':withinRadius'=>$withinRadius,
        );

        //Util::logit($query, __FILE__, __LINE__);

        $prepare = $this->prepareAndExecute($query, $opts, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rst = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $rst;
    }

    function getPlacesWithinLatLong($user_query, $lat, $long, $withinRadius)
    {
        // radius of earth
        // miles - 3959
        // km - 6371

        $radius_earth = 3959;

        // first-cut bounding box (in degrees)
        $maxLat = $lat + rad2deg($withinRadius/$radius_earth);
        $minLat = $lat - rad2deg($withinRadius/$radius_earth);

        // compensate for degrees longitude getting smaller with increasing latitude
        $maxLong = $long + rad2deg($withinRadius/$radius_earth/cos(deg2rad($lat)));
        $minLong = $long - rad2deg($withinRadius/$radius_earth/cos(deg2rad($lat)));

        // convert origin of filter circle to radians
        $lat = deg2rad($lat);
        $long = deg2rad($long);

/*
        $query_bounds =<<<EOQ
            SELECT *
            FROM (
                SELECT
                    info.*,
                    MATCH(info.name, info.notes) AGAINST(:match1) AS info_score,
                    MATCH(mdt.label) AGAINST(:match2) AS mdt_score
                FROM tblMenu menu
                INNER JOIN vMenuStatus status ON status.id = menu.mode_id AND status.menu_status = 'ready'
                INNER JOIN tblMenuInfo_us info ON info.menu_id = menu.id
                INNER JOIN tblMenuMetadata mdt ON mdt.menu_id = menu.id
                WHERE info.latitude > {$minLat} AND info.latitude < {$maxLat}
                AND info.longitude > {$minLong} AND info.longitude < {$maxLong}
                AND (
                    MATCH(info.name, info.notes) AGAINST(:match3) > 0
                    OR MATCH(mdt.label) AGAINST(:match4) > 0
                )
                GROUP BY menu.id
            ) tblPlaces
EOQ;
*/

        $query_match = '';
        if (empty($user_query))
        {
            $query_match =<<<EOQ
                1 AS info_score,
                1 AS mdt_score,
                1 AS info_score_boolean,
                1 AS mdt_score_boolean
EOQ;
        }
        else
        {
            $query_match =<<<EOQ
                MAX(MATCH(info.name, info.notes) AGAINST(:match1)) AS info_score,
                MAX(MATCH(mdtv_label.value) AGAINST(:match2)) AS mdt_score,
                MATCH(info.name, info.notes) AGAINST(:match3 IN BOOLEAN MODE) AS info_score_boolean,
                MATCH(mdtv_label.value) AGAINST(:match4 IN BOOLEAN MODE) AS mdt_score_boolean
EOQ;
        }

        $query_bounds =<<<EOQ
            SELECT *,
                (info_score + mdt_score + info_score_boolean + mdt_score_boolean) AS score
            FROM (
                SELECT
                    info.*,
                    {$query_match}
                FROM tblMenu menu
                INNER JOIN vMenuStatus status ON status.id = menu.mode_id AND status.menu_status = 'ready'
                INNER JOIN tblMenuInfo_us info ON info.menu_id = menu.id
                INNER JOIN tblMenuMetadata mdt ON mdt.menu_id = menu.id
                INNER JOIN tblMenuMetadataValues mdtv_label ON mdt.metadata_id = mdtv_label.metadata_id AND mdtv_label.key = 'label'
                WHERE info.latitude > {$minLat} AND info.latitude < {$maxLat}
                AND info.longitude > {$minLong} AND info.longitude < {$maxLong}
                GROUP BY menu.id
            ) tblPlaces
            WHERE info_score > 0 OR mdt_score > 0
EOQ;

        $query_distance =<<<EOQ
            SELECT
                *,
                ACOS(
                    SIN({$lat})*SIN(RADIANS(latitude)) +
                    COS({$lat})*COS(RADIANS(latitude)) * COS(RADIANS(longitude) - {$long})
                ) * {$radius_earth}  AS distance
            FROM
            (
                {$query_bounds}
            ) AS tblLatLongBounds
EOQ;

        $query =<<<EOQ
            SELECT *
            FROM
            (
                {$query_distance}
            ) AS tblDistance
            WHERE distance < :withinRadius
            ORDER BY score DESC, distance ASC
EOQ;
            //ORDER BY info_score DESC, mdt_score DESC, info_score_boolean DESC, mdt_score_boolean DESC, distance ASC

        $opts = array(
            ':withinRadius'=>$withinRadius,
        );

        if (!empty($user_query))
        {
            $opts[':match1'] = $user_query;
            $opts[':match2'] = $user_query;
            $opts[':match3'] = $user_query;
            $opts[':match4'] = $user_query;
        }

        //Util::logit($query, __FILE__, __LINE__);

        $prepare = $this->prepareAndExecute($query, $opts, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rst = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $rst;
    }

}

