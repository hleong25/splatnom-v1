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

    function getAllSeoItems()
    {
        // 1. menus
        // 2. events

        $query =<<<EOQ
            (
                SELECT
                    'menu' AS `type`,
                    m.id,
                    info.name,
                    m.ts,
                    m.mod_ts
                FROM tblMenu m
                INNER JOIN vMenuStatus ms ON ms.menu_status = 'ready' AND m.mode_id = ms.id
                INNER JOIN tblMenuInfo_us info ON m.id = info.menu_id
            )
            UNION
            (
                SELECT
                    'event' AS `type`,
                    e.id,
                    info.name,
                    e.ts,
                    e.mod_ts
                FROM tblEvent e
                INNER JOIN tblEventInfo_us info ON e.id = info.event_id
            )
            ORDER BY mod_ts DESC
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
