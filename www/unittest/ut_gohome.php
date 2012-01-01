<?php

class ut_gohome extends UnitTest
{
    protected function getUrl()
    {
        return 'http://www.gogomenu.com/home/main';
    }

    protected function getParams()
    {
        $fields = array();
        return $fields;
    }

    protected function validate()
    {
        $this->logit($this->get_curl_getinfo());
        $this->logit(htmlentities($this->get_curl_exec()));
    }
}
