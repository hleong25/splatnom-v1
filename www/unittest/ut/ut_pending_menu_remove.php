<?php

class ut_pending_menu_remove extends Unit_Test
{
    protected $last_id = false;

    public function last_id($last_id)
    {
        $this->last_id = $last_id;
    }

    protected function getUrl()
    {
        return "http://www.gogomenu.com/admin/pendingmenu_list/purge/{$this->last_id}";
    }

    protected function validate()
    {
        $info = $this->get_curl_getinfo();
        $resp_code = $info['http_code'];

        return $resp_code == 200;
    }
}
