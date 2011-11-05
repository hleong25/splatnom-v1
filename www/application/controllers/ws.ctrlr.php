<?php

class WsController
    extends Controller
{
    function __construct ($base_name, $action)
    {
        parent::__construct($base_name, $action);

        $this->m_bRender = false;
    }

    function onAction_agent_trojan()
    {
        // just an outside call to setup the site
        $bAskPasscode = true;
        $code = false;

        $this->set('ask_passcode', false);

        if (isset($_POST['passcode']))
        {
            $code = $_POST['passcode'];
        }
        else if (isset($_GET['passcode']))
        {
            $code = $_GET['passcode'];
        }

        if ($code === PASSCODE_TROJAN)
        {
            $bAskPasscode = false;
        }

        if ($bAskPasscode === true)
        {
            // no passcode set or wrong, so must ask user
            $this->m_bRender = true;
            $this->set('ask_passcode', true);
        }
        else
        {
            // do some actions...

            // finally... redirect to main page
            $this->redirect('/');
        }
    }

    function onAction_search()
    {
        $this->set('query_results', $this->Ws->search());
    }

    function onAction_purge_pending_menu()
    {
        if (empty($_POST) || !isset($_POST['id']))
        {
            $this->set('json', array('status'=>false, 'msg'=>'Invalid argument'));
            return;
        }

        $id = $_POST['id'];
        $menu = new MenuModel();
        $bPurged = $menu->purgePendingMenu($id);

        if (!$bPurged)
            $this->set('json', array('status'=>false, 'msg'=>'Purge failed'));
        else
            $this->set('json', array('status'=>true, 'msg'=>''));
    }
}
