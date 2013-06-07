<?php

class EventModel
    extends Model
{
    function create_new($info)
    {
        $user_id = Util::getUserId();
        if (!$user_id) return false;

        $this->beginTransaction();

        // 1. create the new event
        $query =<<<EOQ
            INSERT INTO tblEvent
            SET
                mode_id = (SELECT id FROM vEventStatus WHERE event_status = 'new'),
                user_id = :user_id
EOQ;

        $params = array(
            ':user_id' => $user_id,
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$this->areDbResultsGood($rst, __FILE__, __LINE__)) return false;

        $event_id = $this->lastInsertId();

        // 2. add initial info to the new event
        $query =<<<EOQ
            INSERT INTO tblEventInfo_us
            SET
                event_id = :event_id,
                name = :name,
                notes = :notes
EOQ;

        $params = array(
            ':event_id' => $event_id,
            ':name' => $info['name'],
            ':notes' => $info['notes'],
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$this->areDbResultsGood($rst, __FILE__, __LINE__)) return false;

        $this->commit();

        return $event_id;
    }

    function add_images($event_id, $imgs)
    {
        $user_id = Util::getUserId();
        if (!$user_id) return false;

        $this->beginTransaction();

        // 1. create prepare stmt
        $query =<<<EOQ
            INSERT INTO tblEventImages
            SET
                event_id = :event_id,
                user_id = :user_id,
                file_img = :file_img,
                width = :width,
                height = :height
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        // 2. add the image to the db linked by event_id
        foreach ($imgs as $img)
        {
            $params = array(
                ':event_id' => $event_id,
                ':user_id' => $user_id,
                ':file_img' => $img['filename'],
                ':width' => $img['width'],
                ':height' => $img['height'],
            );

            $rst = $this->execute_log($prepare, $params, __FILE__, __LINE__);
            if (!$this->areDbResultsGood($rst, __FILE__, __LINE__)) return false;
        }

        // 3. commit it
        $this->commit();

        return true;
    }

    function get_event($event_id)
    {
        // 0. return object
        $event_data = array();

        // 1. get the event info
        $query =<<<EOQ
            SELECT
                name,
                notes,
                address,
                latitude,
                longitude,
                dates,
                '' AS cover_img
            FROM tblEvent event
            LEFT JOIN tblEventInfo_us info ON event.id = info.event_id
            WHERE event.id = :event_id
            LIMIT 1
EOQ;

        $params = array(
            ':event_id' => $event_id,
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$rst) return false;

        $info = $rst->fetchAll(PDO::FETCH_ASSOC);

        if (empty($info))
            return false;

        // 1a. set the return object as the results from db
        $event_data = $info[0];

        // 2. get the event images
        $query =<<<EOQ
            SELECT file_img, width, height
            FROM tblEvent event
            LEFT JOIN tblEventImages imgs ON imgs.event_id = event.id
            WHERE event.id = :event_id
EOQ;

        $params = array(
            ':event_id' => $event_id,
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$rst) return false;

        $imgs = $rst->fetchAll();

        // 2a. the first image is always the cover image
        if (!empty($imgs))
        {
            $event_data['cover_img'] = $imgs[0];
        }

        // TODO: a way to assign cover image

        return $event_data;
    }

    function get_vendors($event_id)
    {
        $query =<<<EOQ
            SELECT
	            vendor.vendor_id,
	            vendor.ordinal,
                vendor_values.key,
                vendor_values.keyindex,
                vendor_values.value
            FROM tblEventVendor vendor
            INNER JOIN tblEventVendorValues vendor_values ON vendor.vendor_id = vendor_values.vendor_id
            WHERE vendor.event_id = :event_id
            ORDER BY vendor.ordinal, vendor_values.key, vendor_values.keyindex
EOQ;

        $params = array(
            ':event_id' => $event_id,
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$rst) return false;

        $vendors = array();
        foreach ($rst as $row)
        {
            $vendor_id = $row['vendor_id'];
            $ordinal = $row['ordinal'];
            $key = $row['key'];
            $value = $row['value'];

            $vendors[$vendor_id]['vendor_id'] = $vendor_id;
            $vendors[$vendor_id]['ordinal'] = $ordinal;

            if (empty($vendors[$vendor_id][$key]))
            {
                $vendors[$vendor_id][$key] = $value;
            }
            else
            {
                $vendors[$vendor_id][$key] .= $value;
            }
        }

        return $vendors;
    }

    function update_event_info($event_id, $info)
    {
        $query =<<<EOQ
            UPDATE tblEvent
            SET
                mod_ts = CURRENT_DATE
            WHERE id = :event_id
EOQ;

        $params = array(
            ':event_id' => $event_id,
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$rst) return false;

        $query =<<<EOQ
            UPDATE tblEventInfo_us
            SET
                name = :name,
                notes = :notes,
                address = :addy,
                latitude = :latitude,
                longitude = :longitude,
                dates = :dates
            WHERE event_id = :event_id
EOQ;

        $params = array(
            ':event_id' => $event_id,
            ':name' => $info['name'],
            ':notes' => $info['notes'],
            ':addy' => $info['address'],
            ':latitude' => $info['latitude'],
            ':longitude' => $info['longitude'],
            ':dates' => $info['dates'],
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$rst) return false;

        return true;
    }

    function update_vendors($event_id, &$vendors)
    {
        $this->beginTransaction();

        $saved_vendor_ids = array();

        foreach ($vendors as &$vendor)
        {
            if (!$this->update_vendor_info($event_id, $vendor))
                return false;

            if (!$this->update_vendor_data($event_id, $vendor))
                return false;

            $saved_vendor_ids[] = $vendor['vendor_id'];
        }

        if (!$this->remove_vendors($event_id, $saved_vendor_ids))
            return false;

        $this->commit();
        return true;
    }

    function update_vendor_info($event_id, &$vendor)
    {
        if (empty($vendor['vendor_id']) || ($vendor['vendor_id'] < 1))
        {
            return $this->insert_vendor_info($event_id, $vendor);
        }

        $query =<<<EOQ
            UPDATE tblEventVendor
            SET
                ordinal = :ordinal
            WHERE vendor_id = :vendor_id
            AND event_id = :event_id
EOQ;

        $params = array(
            ':event_id' => $event_id,
            ':vendor_id' => $vendor['vendor_id'],
            ':ordinal' => $vendor['ordinal'],
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$this->areDbResultsGood($rst, __FILE__, __LINE__)) return false;

        return true;
    }

    function insert_vendor_info($event_id, &$vendor)
    {
        $query =<<<EOQ
            INSERT INTO tblEventVendor
            SET
                event_id = :event_id,
                ordinal = :ordinal
EOQ;

        $params = array(
            ':event_id' => $event_id,
            ':ordinal' => $vendor['ordinal'],
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$this->areDbResultsGood($rst, __FILE__, __LINE__)) return false;

        $vendor['vendor_id'] = $this->lastInsertId();

        return true;
    }

    function update_vendor_data($event_id, &$vendor)
    {
        // 1. clear the data first
        $query =<<<EOQ
            UPDATE tblEventVendorValues
            SET `value` = ''
            WHERE vendor_id = :vendor_id
            AND `key` = :key
EOQ;

        $prepareClearEventValue = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepareClearEventValue) return false;

        // 2. save all items as it's own key/value pair
        $query =<<<EOQ
            INSERT INTO tblEventVendorValues
            SET
                vendor_id = :vendor_id,
                `key` = :key,
                keyindex = :keyindex,
                `value` = :value
            ON DUPLICATE KEY UPDATE
                `value` = :u_value
EOQ;

        $prepareInsertVendorValues = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepareInsertVendorValues) return false;

        foreach ($vendor as $key => $value)
        {
            if ($key === 'vendor_id')
            {
                // skip these keys cause it's not needed
                continue;
            }

            $vendor_id = $vendor['vendor_id'];

            // 1. clear the values first
            $rsts[] = $prepareClearEventValue->bindValue(':vendor_id', $vendor_id);
            $rsts[] = $prepareClearEventValue->bindValue(':key', $key);
            $rsts[] = $prepareClearEventValue->execute();

            if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
            unset($rsts);

            $array_values = Util::str_split_unicode($value, 255);
            if (empty($array_values))
                $array_values[] = '';

            // 2. set the values in chunks
            foreach ($array_values as $key_index => $value_chunk)
            {
                $rsts[] = $prepareInsertVendorValues->bindValue(':vendor_id', $vendor_id);
                $rsts[] = $prepareInsertVendorValues->bindValue(':key', $key);
                $rsts[] = $prepareInsertVendorValues->bindValue(':keyindex', $key_index);
                $rsts[] = $prepareInsertVendorValues->bindValue(':value', $value_chunk);
                $rsts[] = $prepareInsertVendorValues->bindValue(':u_value', $value_chunk);
                $rsts[] = $prepareInsertVendorValues->execute();

                if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
                unset($rsts);
            }
        }

        return true;
    }

    function remove_vendors($event_id, $vendor_ids)
    {
        if (empty($vendor_ids))
            return true;

        // 1. remove vendor data
        $query_in = implode(',', array_fill(0, count($vendor_ids), '?'));
        $query =<<<EOQ
            DELETE FROM tblEventVendorValues
            WHERE vendor_id NOT IN ({$query_in})
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        foreach ($vendor_ids as $idx => $vendor_id)
        {
            $rsts[] = $prepare->bindValue($idx+1, $vendor_id);
        }

        $rsts[] = $prepare->execute();
        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        // 2. remove vendor info
        $query =<<<EOQ
            DELETE FROM tblEventVendor
            WHERE event_id = ?
            AND vendor_id NOT IN ({$query_in})
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rsts[] = $prepare->bindValue(1, $event_id);
        foreach ($vendor_ids as $idx => $vendor_id)
        {
            $rsts[] = $prepare->bindValue($idx+2, $vendor_id);
        }

        $rsts[] = $prepare->execute();
        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        return true;
    }

    function get_upcoming()
    {
        $query =<<<EOQ
            SELECT event_id, name, notes, address, dates
            FROM tblEventInfo_us
            ORDER BY name ASC
EOQ;

        $rst = $this->prepareAndExecute($query, null, __FILE__, __LINE__);
        $rows = $rst->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

}
