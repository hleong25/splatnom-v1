<?php

class ut_menu_import extends Unit_Test
{
    protected $m_menu_id = false;

    public function getNewMenuId()
    {
        return $this->m_menu_id;
    }

    protected function getUrl()
    {
        return 'http://www.gogomenu.com/menu/import';
    }

    protected function validate()
    {
        $html_page = $this->get_curl_exec();

        //logit($html_page);
        $doc = new DOMDocument();
        @$doc->loadHTML($html_page);

        $xpath = new DOMXpath($doc);
        $elems_err = $xpath->query("//div[contains(@class,'error')]");

        if ($elems_err->length > 0)
        {
            foreach ($elems_err as $err)
            {
                echo "Error: {$err->nodeValue}";
            }

            return false;
        }

        $elems = $xpath->query("//div[@class='pg']/a[contains(@href,'edit_metadata')]");

        if ($elems->length == 0)
        {
            echo 'Failed import.';
            return false;
        }

        $new_id_link = $elems->item(0)->attributes->getNamedItem('href')->nodeValue;
        $link_parts = explode('/', $new_id_link);

        if (count($link_parts) != 4)
        {
            echo 'New menu link failed.';
            return false;
        }

        $this->m_menu_id = (int)$link_parts[3];

        echo $this->m_menu_id."\n";
        return $this->m_menu_id > 0;
    }
}
