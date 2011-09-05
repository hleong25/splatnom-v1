<?php

class AdminModel
    extends Model
{
   
    function getUserSearch($q_user)
    {
        $query =<<<EOQ
            SELECT id, username
            FROM tblUsers
            WHERE username LIKE '{$q_user}'
            ORDER BY username
            LIMIT 100
EOQ;
        
        $rst = $this->query($query);
        
        return $rst->fetchAll();
    }
}
