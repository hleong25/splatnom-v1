<?php

class ExportModel
    extends Model
{
    function getMenus()
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT
                m.id,
                m.ts,
                AES_DECRYPT(user.username, '{$aes_key}_username') AS username,
                info.name,
                info.address
            FROM tblMenu m
            INNER JOIN vMenuStatus vms ON (m.mode_id = vms.id) AND (vms.menu_status = 'ready')
            LEFT JOIN tblUsers user ON m.user_id = user.id
            LEFT JOIN tblMenuInfo_us info ON (m.id = info.menu_id)
            ORDER BY m.ts
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

}
