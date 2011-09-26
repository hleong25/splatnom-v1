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
                (SELECT COUNT(menu_id) FROM tblMenuLinks WHERE menu_id = m.id) AS links_cnt,
                (SELECT COUNT(menu_id) FROM tblMenuImages WHERE menu_id = m.id) AS imgs_cnt
            FROM tblMenu m
            INNER JOIN vMenuStatus vms ON (m.mode_id = vms.id) AND (vms.menu_status = 'new')
            GROUP BY m.id
            ORDER BY m.ts
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll();

        return $rows;
    }

}
