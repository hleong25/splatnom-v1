<?php

class TemplateControllerHelper
{
    private $m_template = null;

    function __construct(&$template)
    {
        $this->m_template = &$template;
    }

    function setupNav()
    {
        $links = array();

        $links[] = array('css' => 'nav ', 'lbl' => 'new menu', 'lnk' => 'menu/new');

        if (!UtilsModel::getUserId())
        {
            global $get_url;

            $goto_url = '&goto='.$get_url;

            // not logged in
            $links[] = array('css' => 'nav new_user', 'lbl' => 'register', 'lnk' => 'user/register');
            $links[] = array('css' => 'nav login', 'lbl' => 'login', 'lnk' => "login/main{$goto_url}");
        }
        else
        {
            // logged in
            if(UtilsModel::getPermissions('admin'))
                $links[] = array('css' => 'nav ', 'lbl' => 'admin', 'lnk' => 'admin/main');

            $links[] = array('css' => 'nav logoff', 'lbl' => 'logoff', 'lnk' => 'login/end');
        }

        foreach ($links as $lnk)
        {
            $this->m_template->addNavLinks($lnk);
        }
    }
}
