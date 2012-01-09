<?php

class ut_images_upload extends Unit_Test
{
    protected $m_menu_id = false;
    protected $m_imgs = array();

    public function set_menu_id($menu_id)
    {
        $this->m_menu_id = $menu_id;
    }

    public function get_imgs()
    {
        return $this->m_imgs;
    }

    protected function getUrl()
    {
        return "http://www.gogomenu.com/images/upload/{$this->m_menu_id}";
    }

    protected function validate()
    {
        $html_page = $this->get_curl_exec();

        // get last table row
        $doc = new DOMDocument();
        @$doc->loadHTML($html_page);

        $xpath = new DOMXpath($doc);
        $elem_imgs = $xpath->query("//img[contains(@src,'/images/get/menu')]");

        if ($elem_imgs->length == 0)
            return false;

        foreach ($elem_imgs as $elem_img)
        {
            $img = $elem_img->attributes->getNamedItem('src')->nodeValue;
            $this->m_imgs[] = $img;
        }

        return true;
    }
}
