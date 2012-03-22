<?php

class HomeModel
    extends Model
{
    function getMenuNeedsMetadata()
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT
                m.id,
                m.ts,
                info.name,
                (SELECT COUNT(menu_id) FROM tblMenuLinks WHERE menu_id = m.id) AS links_cnt,
                (SELECT COUNT(menu_id) FROM tblMenuImages WHERE menu_id = m.id) AS imgs_cnt,
                AES_DECRYPT(user.username, '{$aes_key}_username') AS username
            FROM tblMenu m
            INNER JOIN vMenuStatus vms ON (m.mode_id = vms.id) AND (vms.menu_status = 'new')
            LEFT JOIN tblMenuInfo_us info ON (m.id = info.menu_id)
            LEFT JOIN tblUsers user ON m.user_id = user.id
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
            ORDER BY m.mod_ts DESC
            LIMIT 12
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll();
        return $rows;
    }

}
