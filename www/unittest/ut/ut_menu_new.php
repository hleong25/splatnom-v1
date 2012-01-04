<?php

class ut_menu_new extends Unit_Test
{
    protected function getUrl()
    {
        return 'http://www.gogomenu.com/menu/new';
    }

    protected function validate()
    {
        $html_page = $this->get_curl_exec();

        $doc = new DOMDocument();
        @$doc->loadHTML($html_page);

        $xpath = new DOMXpath($doc);
        $elem = $xpath->query("//span[@id='msg']");

        if ($elem->length > 0)
        {
            return true;
        }

        return false;
    }
}
