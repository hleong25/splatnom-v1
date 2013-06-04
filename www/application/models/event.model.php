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
                GROUP_CONCAT(vendor_values.value ORDER BY vendor_values.keyindex ASC SEPARATOR '') AS `value`
            FROM tblEventVendor vendor
            INNER JOIN tblEventVendorValues vendor_values ON vendor.vendor_id = vendor_values.vendor_id
            WHERE vendor.event_id = :event_id
            GROUP BY vendor_values.vendor_id, vendor_values.key
            ORDER BY vendor.ordinal
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
            $vendors[$vendor_id][$key] = $value;
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
                $saved_vendor_ids[] = $vendor['vendor_id'];
        }

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

        $rsts[] = $prepareClearEventValue->bindValue(':vendor_id', $vendor['vendor_id']);
        $rsts[] = $prepareClearEventValue->bindValue(':key', 'name');
        $rsts[] = $prepareClearEventValue->execute();

        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        $rsts[] = $prepareClearEventValue->bindValue(':vendor_id', $vendor['vendor_id']);
        $rsts[] = $prepareClearEventValue->bindValue(':key', 'description');
        $rsts[] = $prepareClearEventValue->execute();

        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        // 2. save the name
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

        $rsts[] = $prepareInsertVendorValues->bindValue(':vendor_id', $vendor['vendor_id']);

        $rsts[] = $prepareInsertVendorValues->bindValue(':key', 'name');
        $rsts[] = $prepareInsertVendorValues->bindValue(':keyindex', 0);
        $rsts[] = $prepareInsertVendorValues->bindValue(':value', $vendor['name']);
        $rsts[] = $prepareInsertVendorValues->bindValue(':u_value', $vendor['name']);
        $rsts[] = $prepareInsertVendorValues->execute();

        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        // 3. save the description
        $array_values = Util::str_split_unicode($vendor['description'], 255);

        $rsts[] = $prepareInsertVendorValues->bindValue(':key', 'description');
        foreach ($array_values as $key_index => $value_chunk)
        {
            $rsts[] = $prepareInsertVendorValues->bindValue(':keyindex', $key_index);
            $rsts[] = $prepareInsertVendorValues->bindValue(':value', $value_chunk);
            $rsts[] = $prepareInsertVendorValues->bindValue(':u_value', $value_chunk);
            $rsts[] = $prepareInsertVendorValues->execute();

            if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
            unset($rsts);
        }

        return true;
    }

}
