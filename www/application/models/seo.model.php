<?php

class SeoModel
    extends Model
{
    function getAllMenus()
    {
        $query =<<<EOQ
            SELECT
                m.id,
                info.name,
                m.ts,
                m.mod_ts
            FROM tblMenu m
            INNER JOIN vMenuStatus ms ON ms.menu_status = 'ready' AND m.mode_id = ms.id
            INNER JOIN tblMenuInfo_us info ON m.id = info.menu_id
            ORDER BY m.mod_ts DESC
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
