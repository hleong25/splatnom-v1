<?php

class ut_golink extends Unit_Test
{
    protected $m_link = false;

    public function link($link)
    {
        $this->m_link = $link;
    }

    public function curl_exec()
    {
        return $this->get_curl_exec();
    }

    public function curl_getinfo()
    {
        return $this->get_curl_getinfo();
    }

    protected function getUrl()
    {
        return $this->m_link;
    }

    protected function validate()
    {
        return true;
    }
}
