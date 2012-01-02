<?php

class ut_login extends Unit_Test
{
    protected $m_is_bad_login = false;

    public function bad_login()
    {
        $this->m_is_bad_login = true;
    }

    protected function isNewSession()
    {
        return true;
    }

    protected function getUrl()
    {
        return 'http://www.gogomenu.com/login/main&goto=home/main';
    }

    protected function validate()
    {
        $info = $this->get_curl_getinfo();
        $resp_code = $info['http_code'];

        if (!$this->m_is_bad_login && $resp_code == 302)
            return true;

        if ($this->m_is_bad_login && $resp_code == 200)
            return true;

        return false;
    }
}
