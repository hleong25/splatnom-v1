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
                notes = :notes,
                ts_start = CURRENT_DATE,
                ts_end = CURRENT_DATE
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
    }
}
