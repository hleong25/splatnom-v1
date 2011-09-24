<?php

class TrojanModel
    extends Model
{
    function init_admin()
    {
        /*
            This will set the first user as admin
        */

        $query =<<<EOQ
            INSERT IGNORE INTO tblUserPermissions(user_id, permission_id)
            VALUES ((SELECT id FROM tblUsers ORDER BY id ASC LIMIT 1),
            (SELECT id FROM vPermissions WHERE permission='admin'));
EOQ;

        $rst = $this->query_log($query, __FILE__, __LINE__);
        if (!$rst)
            return false;
        $rst->closeCursor();
        unset($rst);

        $query =<<<EOQ
            SELECT user_id, validation_code AS code
            FROM tblUserValidation
            WHERE user_id = (SELECT id FROM tblUsers ORDER BY id ASC LIMIT 1)
EOQ;

        $rst = $this->query_log($query, __FILE__, __LINE__);
        if (!$rst)
            return false;

        $row = $rst->fetchAll(PDO::FETCH_ASSOC);
        $row = array_shift($row);
        if (empty($row))
            return false;

        $user_id = $row['user_id'];
        $code = $row['code'];

        $user = new UserModel();
        $rst = $user->verifyUserCode($user_id, $code);

        return true;
    }

}

