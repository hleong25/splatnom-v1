<?php
// http://www.movable-type.co.uk/scripts/latlong-db.html

class LocationModel
    extends Model
{
    function getLatLongByZip($zip)
    {
        $query =<<<EOQ
            SELECT
                latitude, longitude
            FROM tblLocation_us
            WHERE `country code` = 'US'
            AND `postal code` = :zip
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':zip'=>$zip), __FILE__, __LINE__);
        if (!$prepare) return false;

        $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $row = array_shift($rows);
        return $row;
    }

    function parseCityState($citystate)
    {
        $split = explode(',', $citystate);
        $n_split = count($split);
        if ($n_split < 2)
            return false;

        $city = trim($split[$n_split -2]);
        $state = trim($split[$n_split -1]);

        return array('city'=>$city, 'state'=>$state);
    }

    function getLatLongByCityState($city, $state)
    {
        $query =<<<EOQ
            SELECT
                AVG(DISTINCT latitude) AS latitude,
                AVG(DISTINCT longitude) AS longitude
            FROM tblLocation_us
            WHERE `country code` = 'US'
            AND `place name` = :city
            AND `state code1` = :state
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':city'=>$city, ':state'=>$state), __FILE__, __LINE__);
        if (!$prepare) return false;

        $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $row = array_shift($rows);
        return $row;

    }

    function getLocationsByAddress($address, $state_filters)
    {
        $query_in = implode(',', array_fill(0, count($state_filters), '?'));
        $query =<<<EOQ
            SELECT * FROM
            (
                SELECT
                    latitude, longitude,
                    `postal code` AS zip,
                    `place name` AS city, `state code1` AS state,
                    MATCH(`state code1`, `place name`) AGAINST(?) AS score
                FROM tblLocation_us
                WHERE MATCH(`state code1`, `place name`) AGAINST(?)
                AND `state code1` IN ({$query_in})
            ) tblLocations
            ORDER BY score DESC, zip
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rst = $prepare->bindValue(1, $address);
        $rst = $prepare->bindValue(2, $address);

        foreach ($state_filters as $idx => $filter)
        {
            $rst = $prepare->bindValue($idx+3, $filter);
            if (!$rst) return false;
        }

        $rst = $prepare->execute();
        if (!$rst) return false;

        $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

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

        $query_bounds =<<<EOQ
            SELECT *,
                (info_score + mdt_score + info_score_boolean + mdt_score_boolean) AS score
            FROM (
                SELECT
                    info.*,
                    MAX(MATCH(info.name, info.notes) AGAINST(:match1)) AS info_score,
                    MAX(MATCH(mdt.label) AGAINST(:match2)) AS mdt_score,
                    MATCH(info.name, info.notes) AGAINST(:match3 IN BOOLEAN MODE) AS info_score_boolean,
                    MATCH(mdt.label) AGAINST(:match4 IN BOOLEAN MODE) AS mdt_score_boolean
                FROM tblMenu menu
                INNER JOIN vMenuStatus status ON status.id = menu.mode_id AND status.menu_status = 'ready'
                INNER JOIN tblMenuInfo_us info ON info.menu_id = menu.id
                INNER JOIN tblMenuMetadata mdt ON mdt.menu_id = menu.id
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
            ':match1'=>$user_query,
            ':match2'=>$user_query,
            ':match3'=>$user_query,
            ':match4'=>$user_query,
            ':withinRadius'=>$withinRadius,
        );

        $prepare = $this->prepareAndExecute($query, $opts, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rst = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $rst;
    }

}

