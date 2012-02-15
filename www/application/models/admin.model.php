<?php

class AdminModel
    extends Model
{

    function getUserSearch($q_user)
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT id, username, email, firstname, lastname
            FROM
                (
                    SELECT
                        id,
                        AES_DECRYPT(username, '{$aes_key}_username') AS username,
                        AES_DECRYPT(email, '{$aes_key}_email') AS email,
                        AES_DECRYPT(firstname, '{$aes_key}_firstname') AS firstname,
                        AES_DECRYPT(lastname, '{$aes_key}_lastname') AS lastname
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
