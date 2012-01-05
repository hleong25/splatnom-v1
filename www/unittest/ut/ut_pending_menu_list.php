<?php

class ut_pending_menu_list extends Unit_Test
{
    protected $m_last_id = false;

    public function get_last_pending_id()
    {
        return $this->m_last_id;
    }

    protected function getUrl()
    {
        return 'http://www.gogomenu.com/admin/pendingmenu_list';
    }

    protected function validate()
    {
        $html_page = $this->get_curl_exec();

        // get last table row
        $doc = new DOMDocument();
        @$doc->loadHTML($html_page);

        $xpath = new DOMXpath($doc);
        $elems = $xpath->query("(//table[@class='tblDefault']/tbody/tr)[last()]");

        if ($elems->length == 0)
            return false;

        $elem_tr = $elems->item(0);
        $xml = $doc->saveXML($elem_tr);

        // get pending id
        @$doc->loadHTML($xml);

        $xpath = new DOMXpath($doc);
        $elems = $xpath->query('//tr/td[1]');

        if ($elems->length == 0)
            return false;

        $elem_td = $elems->item(0);

        $last_id = (int) $elem_td->nodeValue;
        $this->m_last_id = $last_id;

        return true;
    }
}
