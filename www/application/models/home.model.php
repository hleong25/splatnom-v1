<?php

class HomeModel
    extends Model
{
    function getNewlyAddedMenus()
    {
        $query =<<<EOQ
            SELECT pc.id, pc.name, pi.file_img
            FROM tblPlaceContact pc
            LEFT JOIN tblPlaceImages pi ON pc.id = pi.place_id
            GROUP BY pc.id
            ORDER BY pc.id DESC
            LIMIT 10
EOQ;
        
        $rst = $this->query($query);
        
        return $rst->fetchAll();
    }
}