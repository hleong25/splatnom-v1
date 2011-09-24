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
}

