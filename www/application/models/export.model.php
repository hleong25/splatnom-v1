<?php

class ExportModel
    extends Model
{
    static function menu_normalize(&$datas)
    {
        // clear status
        unset($datas['info']['status']);

        // clear the section_id and metadata_id
        foreach ($datas['metadatas'] as &$mtd)
        {
            $mtd['section_id'] = -1;

            foreach ($mtd['items'] as &$item)
            {
                $item['metadata_id'] = -1;
            }
        }

        // reset the array of images to be just image name
        foreach ($datas['imgs'] as $idx => $img)
        {
            $datas['imgs'][$idx] = $img['filename'];
        }
    }

    function getMenus()
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT
                m.id,
                m.ts,
                m.mod_ts,
                AES_DECRYPT(user.username, '{$aes_key}_username') AS username,
                info.name,
                info.address
            FROM tblMenu m
            INNER JOIN vMenuStatus vms ON (m.mode_id = vms.id) AND (vms.menu_status = 'ready')
            LEFT JOIN tblUsers user ON m.user_id = user.id
            LEFT JOIN tblMenuInfo_us info ON (m.id = info.menu_id)
            ORDER BY m.mod_ts DESC
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

}
