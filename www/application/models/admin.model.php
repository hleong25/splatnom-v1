<?php

class AdminModel
    extends Model
{
    function getNewMenuCount()
    {
        $query =<<<EOQ
            SELECT COUNT(*) AS cnt 
            FROM tblNewMenu
EOQ;
        
        $rst = $this->query($query);
        $cnt = (int) $rst->fetchColumn();
        
        return $cnt;
    }
    
    function getNewMenus()
    {
        $query =<<<EOQ
            SELECT menu.*, COUNT(imgs.newmenu_id) as cnt_imgs
            FROM tblNewMenu menu
            LEFT JOIN tblNewMenuImages imgs ON menu.id = imgs.newmenu_id
            GROUP BY menu.id
EOQ;
        
        $rst = $this->query($query);
        $rows = $rst->fetchAll();
        
        foreach ($rows as $key => $row)
        {
            $site = $row['site_addy'];
            if (stristr($site, 'http://'))
            {
                // if PHP >= 5.3 then do 
                // $site = stristr($site, 'http://', true)

                $site = substr($site, 7);
            }

            if (stristr($site, 'https://'))
            {
                // if PHP >= 5.3 then do 
                // $site = stristr($site, 'https://', true)

                $site = substr($site, 8);
            }
            
            $rows[$key]['site_addy'] = $site;
        }
        
        return $rows;
    }
    
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