<?php

class ImportController
    extends Controller
{
    function onAction_list($remote_site=null)
    {
        if (!Util::getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }
        else if (!empty($_GET) && isset($_GET['remote_site']))
        {
            $this->redirect('/import/list/'.$_GET['remote_site']);
            return;
        }

        $import = $this->Import;

        $this->set('remote_site', $remote_site);

        if (!empty($remote_site))
        {
            $this->addCss('table');

            $list = $import->getList($remote_site);
            $this->set('remote_menus', $list);
        }
    }

    function onAction_menus($remote_site=null, $id=null)
    {
        if (!Util::getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }

        if (empty($id) && empty($_POST['menu_ids']))
        {
            $this->redirect('/import/list/'.$remote_site);
            return;
        }

        $menu_ids = array();

        if (!empty($id))
            $menu_ids[] = $id;

        if (!empty($_POST['menu_ids']))
            $menu_ids = $_POST['menu_ids'];

        if (empty($menu_ids))
        {
            $this->redirect('/import/list'.$remote_site);
            return;
        }

        $import = $this->Import;

        $this->set('remote_site', $remote_site);

        if (!empty($remote_site))
        {
            $import_menus = $import->getMenus($remote_site, $menu_ids);
            $this->set('dbg', $import_menus);
            $this->set('remote_menus', $import_menus);
        }
    }
}
