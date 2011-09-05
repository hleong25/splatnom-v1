<?php

class UserModel
    extends Model
{
    function isUsernameAvailable($q_name)
    {
        $query =<<<EOQ
            SELECT COUNT(*) AS cnt
            FROM tblUsers
            WHERE username = :name
EOQ;
        
        $prepare = $this->prepareAndExecute($query, array(':name' => $q_name), __FILE__, __LINE__);
        if (!$prepare) return false;
        
        $cnt = $prepare->fetchColumn();
        return $cnt == 0;
    }
    
    /*
     * @Function addNewUser
     * @param (mixed array) $info = array('username', 'password')
     */
    function addNewUser($info)
    {
        $this->beginTransaction();
        
        $query =<<<EOQ
            INSERT INTO tblUsers(
                username
            ) VALUES (
                :username
            )
EOQ;
        
        $params = array(
            ':username' => $info['username'],
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
            ON DUPLICATE KEY UPDATE password = :password
EOQ;
        
        $params = array(
            ':user_id' => $id,
            ':password' => sha1($password),
        );
        
        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;
        
        return true;
    }
    
    function getUser($id)
    {
        $query =<<<EOQ
            SELECT *
            FROM tblUsers
            WHERE id = :id
EOQ;
        
        $prepare = $this->prepareAndExecute($query, array(':id' => $id), __FILE__, __LINE__);
        if (!$prepare) return false;
        
        $cnt = $prepare->rowCount();
        if ($cnt != 0)
        {
            // bad id
            return false;
        }
        
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
}
