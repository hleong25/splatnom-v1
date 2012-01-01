<?php

class ut_login extends UnitTest
{
    protected $m_new_session = false;

    protected function getUrl()
    {
        return 'http://www.gogomenu.com/login/main&goto=home/main';
    }

    protected function getParams()
    {
        $fields = array
        (
            'lu' => 'admin',
            'lp' => 'henry',
        );

        return $fields;
    }

    protected function validate()
    {
        $this->logit($this->get_curl_getinfo());
        $this->logit(htmlentities($this->get_curl_exec()));
    }
}
