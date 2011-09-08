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

    function getMenuNeedsMetadata()
    {
        /*
            select m.id, m.ts, m.site_addy, count(mi.id) imgs_cnt
            from tblMenu m
            left join vMenuStatus vms on m.mode_id = vms.id and vms.menu_status = 'new'
            left join tblMenuImages mi on m.id = mi.menu_id
            group by mi.menu_id
            order by m.ts
        */

        $query =<<<EOQ
            SELECT
                m.id,
                m.ts,
                m.site_addy,
                COUNT(mi.id) imgs_cnt
            FROM tblMenu m
            LEFT JOIN vMenuStatus vms ON (m.mode_id = vms.id) AND (vms.menu_status = 'new')
            LEFT JOIN tblMenuImages mi ON m.id = mi.menu_id
            GROUP BY mi.menu_id
            ORDER BY m.ts
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll();

        return $rows;
    }

}
