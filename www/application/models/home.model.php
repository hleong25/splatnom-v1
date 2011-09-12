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
                m.site_addy,
                COUNT(mi.id) imgs_cnt
            FROM tblMenu m
            INNER JOIN vMenuStatus vms ON (m.mode_id = vms.id) AND (vms.menu_status = 'new')
            LEFT JOIN tblMenuImages mi ON m.id = mi.menu_id
            GROUP BY mi.menu_id
            ORDER BY m.ts
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll();

        return $rows;
    }

}
