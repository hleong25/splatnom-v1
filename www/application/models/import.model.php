<?php

class ImportModel
    extends Model
{
    static function menu_normalize(&$datas)
    {
        $ordinal_section = 0;
        $ordinal_item = 0;

        // make sure section_id and metadata_id are -1
        foreach ($datas['metadatas'] as &$mtd)
        {
            $mtd['section_id'] = -1;
            $mtd['ordinal'] = $ordinal_section++;

            $ordinal_item = 0;
            foreach ($mtd['items'] as &$item)
            {
                $item['metadata_id'] = -1;
                $item['ordinal'] = $ordinal_item++;
            }
        }
    }

    function getCustomHeaders()
    {
        $headers = array();
        $headers[] = 'X-SPLATNOM-REMOTE: '.Util::getTopLevelDomain();

        return $headers;
    }

    function getList($server)
    {
        $url = "http://{$server}/export/list/json";
        $headers = $this->getCustomHeaders();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $file_content = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($file_content, true);
        return $json;
    }

    private $m_curl_headers;

    function getMenus($server, $menu_ids)
    {
        $url = "http://{$server}/export/menus";
        $headers = $this->getCustomHeaders();

        $tmp_src = OS_TEMP_PATH.DS.uniqid('menu_import.').'.zip';
        $fp = fopen($tmp_src, 'w');
        if ($fp === false)
        {
            Util::logit("Failed to create temp file: {$tmp_src}", __FILE__, __LINE__);
            return false;
        }

        $post = array();
        $post['menu_ids'] = $menu_ids;

        // clear the headers
        $this->m_curl_headers = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'readCurlHeaders'));
        $exec_res = curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        // we got the file... lets rename it to the original filename
        $tmp_dst = @$this->m_curl_headers['X-SPLATNOM-FILENAME'];
        if (!empty($tmp_dst))
        {
            $tmp_dst = OS_TEMP_PATH.DS.$tmp_dst;
            @rename($tmp_src, $tmp_dst);
        }
        else
        {
            $tmp_dst = $tmp_src;
        }

        //Util::logit("Saved imported file: {$tmp_dst}");

        return $tmp_dst;
    }

    function readCurlHeaders($ch, $header)
    {
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        @list($key, $val) = explode(':', $header, 2);

        $key = trim($key);
        $val = trim($val);
        $this->m_curl_headers[$key] = $val;

        return strlen($header);
    }

}
