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
                dates
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
}
