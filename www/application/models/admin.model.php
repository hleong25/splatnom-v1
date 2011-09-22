<?php

class AdminModel
    extends Model
{

    function getUserSearch($q_user)
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT id, username
            FROM
                (
                    SELECT id, AES_DECRYPT(username, '{$aes_key}_username') AS username
                    FROM tblUsers
                ) tblDecryptedUsers
            WHERE username LIKE '{$q_user}'
            ORDER BY username
            LIMIT 100

EOQ;

        $rst = $this->query($query);

        return $rst->fetchAll();
    }
}
