<?php

class UserModel
    extends Model
{
    function isUsernameAvailable($q_name)
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT COUNT(*) AS cnt
            FROM tblUsers
            WHERE username = AES_ENCRYPT(:name, '{$aes_key}_username')
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':name' => $q_name), __FILE__, __LINE__);
        if (!$prepare) return false;

        $cnt = $prepare->fetchColumn();
        return $cnt == 0;
    }

    /*
     * @Function addNewUser
     * @param (mixed array) $info = array('firstname', 'lastname', 'email', 'username', 'password')
     */
    function addNewUser($info)
    {
        $this->beginTransaction();

        /*
            [parameter => max string length]
            firstname => 30
            lastname => 30
            email => 100
            username => 50
            password => 100

            [string length => aes length]
            30  => 46
            50  => 66
            100 => 116
        */

        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            INSERT INTO tblUsers(
                status,
                username,
                email,
                firstname,
                lastname
            ) VALUES (
                (SELECT id FROM vUserStatus WHERE user_status = 'new'),
                AES_ENCRYPT(:username, '{$aes_key}_username'),
                AES_ENCRYPT(:email, '{$aes_key}_email'),
                AES_ENCRYPT(:firstname, '{$aes_key}_firstname'),
                AES_ENCRYPT(:lastname, '{$aes_key}_lastname')
            )
EOQ;

        $params = array(
            ':username' => $info['username'],
            ':email' => $info['email'],
            ':firstname' => $info['firstname'],
            ':lastname' => $info['lastname'],
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $user_id = $this->lastInsertId();

        $bSetPassword = $this->setUserPassword($user_id, $info['password']);
        if (!$bSetPassword)
            return false;

        $this->commit();
        return $user_id;
    }

    function setUserPassword($id, $password)
    {
        $query =<<<EOQ
            INSERT INTO tblUserPasswords(
                user_id,
                password
            ) VALUES (
                :user_id,
                :password
            )
            ON DUPLICATE KEY UPDATE password = :u_password
EOQ;

        $sha_password = sha1($password);

        $params = array(
            ':user_id' => $id,
            ':password' => $sha_password,
            ':u_password' => $sha_password,
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        return true;
    }

    function getUser($id)
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT
                id,
                ts,
                AES_DECRYPT(username, '{$aes_key}_username') AS username,
                AES_DECRYPT(email, '{$aes_key}_email') AS email,
                AES_DECRYPT(firstname, '{$aes_key}_firstname') AS firstname,
                AES_DECRYPT(lastname, '{$aes_key}_lastname') AS lastname
            FROM tblUsers
            WHERE id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':id' => $id), __FILE__, __LINE__);
        if (!$prepare) return false;

        $info = $prepare->fetch(PDO::FETCH_ASSOC);
        return $info;
    }

    function getUserByUsername($username)
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT
                id,
                ts,
                AES_DECRYPT(username, '{$aes_key}_username') AS username,
                AES_DECRYPT(email, '{$aes_key}_email') AS email,
                AES_DECRYPT(firstname, '{$aes_key}_firstname') AS firstname,
                AES_DECRYPT(lastname, '{$aes_key}_lastname') AS lastname
            FROM tblUsers
            WHERE username = AES_ENCRYPT(:name, '{$aes_key}_username')
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':name' => $username), __FILE__, __LINE__);
        if (!$prepare) return false;

        $info = $prepare->fetch(PDO::FETCH_ASSOC);
        return $info;
    }

    function getUserPermission($id)
    {
        $query =<<<EOQ
            SELECT
                p.permission,
                NOT ISNULL(up.user_id) AS 'Assigned'
            FROM vPermissions p
            LEFT JOIN tblUserPermissions up ON up.permission_id = p.id AND up.user_id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':id' => $id), __FILE__, __LINE__);
        if (!$prepare) return false;

        $permissions = array();
        $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $perm)
        {
            $permissions[$perm['permission']] = $perm['Assigned'] == 1;
        }

        return $permissions;
    }

    function setUserPermissions($id, $perms)
    {
        $this->beginTransaction();

        $query =<<<EOQ
            SELECT id, permission
            FROM vPermissions
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll(PDO::FETCH_ASSOC);
        $cachePerms = array();
        foreach ($rows as $row)
            $cachePerms[$row['permission']] = $row['id'];

        $query =<<<EOQ
            DELETE FROM tblUserPermissions
            WHERE user_id = :user_id
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':user_id' => $id), __FILE__, __LINE__);
        if (!$prepare) return false;

        $query =<<<EOQ
            INSERT INTO tblUserPermissions(
                user_id,
                permission_id
            ) VALUES (
                :user_id,
                :perm_id
            )
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        foreach($perms as $key)
        {
            if (!array_key_exists($key, $cachePerms))
                continue;

            $p = array
            (
                ':user_id' => $id,
                ':perm_id' => $cachePerms[$key]
            );

            $rst = $this->execute_log($prepare, $p, __FILE__, __LINE__);
        }

        $this->commit();
    }

    function setUserVerifyCode($id)
    {
        $validation_code = Util::getRandomString();

        $query =<<<EOQ
            INSERT INTO tblUserValidation(
                user_id,
                validation_code
            ) VALUES (
                :user_id,
                :validation_code
            ) ON DUPLICATE KEY UPDATE validation_code = :u_validation_code
EOQ;

        $params = array(
            ':user_id' => $id,
            ':validation_code' => $validation_code,
            ':u_validation_code' => $validation_code,
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        return $validation_code;
    }

    function verifyUserCode($id, $code)
    {
        $query =<<<EOQ
            SELECT COUNT(*) AS cnt
            FROM tblUserValidation
            WHERE user_id = :user_id
            AND validation_code = :code
EOQ;

        $params = array(':user_id'=>$id, ':code'=>$code);

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
            DELETE FROM tblUserValidation
            WHERE user_id = :user_id
            AND validation_code = :code
EOQ;

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $query =<<<EOQ
            UPDATE tblUsers
            SET status = (SELECT id FROM vUserStatus WHERE user_status = 'validated')
            WHERE id = :user_id
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':user_id'=>$id), __FILE__, __LINE__);
        if (!$prepare) return false;

        $this->commit();
        return true;
    }

    function getUserMenus($user_id)
    {
        // NOTE: copied and modified from HomeModel::getReadyMenus()

        $query =<<<EOQ
            SELECT
                m.id,
                m.ts,
                ms.menu_status,
                info.name,
                info.address
            FROM tblMenu m
            INNER JOIN vMenuStatus ms ON m.mode_id = ms.id
            LEFT JOIN tblMenuInfo_us info ON m.id = info.menu_id
            WHERE m.user_id = :user_id
            ORDER BY ms.ordinal, m.ts DESC
EOQ;

        $rst = $this->prepareAndExecute($query, array(':user_id' => $user_id), __FILE__, __LINE__);
        $rows = $rst->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
