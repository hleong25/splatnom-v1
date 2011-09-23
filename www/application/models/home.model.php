<?php

class HomeModel
    extends Model
{
    function getMenuNeedsMetadata()
    {
        $query =<<<EOQ
            SELECT
                m.id,
                m.ts,
                COUNT(ml.menu_id) AS links_cnt,
                COUNT(mi.id) AS imgs_cnt
            FROM tblMenu m
            INNER JOIN vMenuStatus vms ON (m.mode_id = vms.id) AND (vms.menu_status = 'new')
            LEFT JOIN tblMenuLinks ml ON m.id = ml.menu_id
            LEFT JOIN tblMenuImages mi ON m.id = mi.menu_id
            GROUP BY m.id
            ORDER BY m.ts
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll();

        return $rows;
    }

}
