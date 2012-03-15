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

    function set_reset_code($user, $reset_code)
    {
        $aes_key = SQL_AES_KEY;

        $query_user =<<<EOQ
            SELECT id
            FROM tblUsers
            WHERE username = AES_ENCRYPT(:username, '{$aes_key}_username')
EOQ;

        $query =<<<EOQ
            INSERT INTO tblResetPassword
            SET
                user_id = ({$query_user}),
                reset_code = :reset_code
            ON DUPLICATE KEY UPDATE
                ts = CURRENT_TIMESTAMP,
                reset_code = :u_reset_code
EOQ;

        $params = array(
            ':username' => $user,
            ':reset_code' => $reset_code,
            ':u_reset_code' => $reset_code,
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare)
        {
            Util::logit("Failed to set reset code for '{$user}'.", __FILE__, __LINE__);
            return false;
        }

        return $reset_code;
    }

    function verify_reset_code($user_id, $reset_code)
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT COUNT(*) AS cnt
            FROM tblResetPassword
            WHERE user_id = :user_id
            AND reset_code = :reset_code
EOQ;

        $params = array(':user_id'=>$user_id, ':reset_code'=>$reset_code);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $cnt = $prepare->fetchColumn();
        if ($cnt == 0)
            return false;

        $prepare->closeCursor();
        unset($prepare);

        // since we're here, it's good... so delete it and update the user status
        $this->beginTransaction();

        $query =<<<EOQ
            DELETE FROM tblResetPassword
            WHERE user_id = :user_id
            AND reset_code = :reset_code
EOQ;

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $this->commit();
        return true;
    }
}
