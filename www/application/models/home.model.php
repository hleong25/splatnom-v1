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
                info.name,
                (SELECT COUNT(menu_id) FROM tblMenuLinks WHERE menu_id = m.id) AS links_cnt,
                (SELECT COUNT(menu_id) FROM tblMenuImages WHERE menu_id = m.id) AS imgs_cnt
            FROM tblMenu m
            INNER JOIN vMenuStatus vms ON (m.mode_id = vms.id) AND (vms.menu_status = 'new')
            LEFT JOIN tblMenuInfo_us info ON (m.id = info.menu_id)
            GROUP BY m.id
            ORDER BY m.ts
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll();

        return $rows;
    }

    function getReadyMenus()
    {
        $query =<<<EOQ
            SELECT
                m.id,
                info.name,
                info.address
            FROM tblMenu m
            INNER JOIN vMenuStatus ms ON ms.menu_status = 'ready' AND m.mode_id = ms.id
            INNER JOIN tblMenuInfo_us info ON m.id = info.menu_id
            ORDER BY m.ts
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll();
        return $rows;
    }

}
