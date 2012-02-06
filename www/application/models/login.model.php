<?php

class LoginModel
    extends Model
{
    function tryLogin($user, $pass)
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT u.id
            FROM tblUsers u
            INNER JOIN tblUserPasswords p ON u.id = p.user_id
            WHERE u.username = AES_ENCRYPT(:username, '{$aes_key}_username')
            AND p.password = :password
EOQ;

        $params = array(
            ':username' => $user,
            ':password' => sha1($pass),
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        // fetchColumn will return false if nothing
        $rst = $prepare->fetchColumn();
        return $rst;
    }
}
