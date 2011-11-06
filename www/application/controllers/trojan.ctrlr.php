<?php

class TrojanController
    extends Controller
{
    function __construct ($base_name, $action)
    {
        parent::__construct($base_name, $action);

        $this->m_bRender = false;
    }

    function onAction_init_admin()
    {
        $rst = $this->Trojan->init_admin();
        $this->redirect('/home/main');
    }

    function onAction_check_system()
    {
        $system = array();

        if (!function_exists('gd_info'))
        {
            $system[] = array(
                'req' => 'gd library',
                'fix' => 'install php-gd on centos',
                'hint' => 'yum install php-gd',
            );
        }

        if (!empty($system))
        {
            $this->m_bRender = true;
            $this->set('system', $system);
        }
        else
        {
            $this->redirect('/home/main');
        }
    }
}

