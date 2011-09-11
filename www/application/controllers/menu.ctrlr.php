<?php

class MenuController
    extends Controller
{
    function onAction_new()
    {
        $new_menu_done = !empty($_POST);

        $this->set('new_menu_done', $new_menu_done);

        $this->addCss('menu/menu.new');
        $this->addJs('menu/menu.new');

        if ($new_menu_done === true)
        {
            $addy = $_POST['site_menu'];
            $this->Menu->saveNewMenu($addy);
        }
    }

    function onAction_show($id = null)
    {
        if (!empty($id))
        {
            $this->show_menu($id);
        }
        else if (!empty($_POST))
        {
            //$this->save_new_menu();
            error_log('onAction_show');
        }
        else
        {
            // epic fail...
            $this->redirect('/menu/new');
            $_SESSION['save_failed_msg'] = 'Epic failed...';
            $_SESSION['save_failed_data'] = $_POST;
        }

    }

    function show_menu($id)
    {
        $info = $this->Menu->getMenu($id);

        $this->set('menu_id', $id);
        $this->set('info', $info);
    }

    function onAction_edit($id = null)
    {
        $this->addCss('menu/menu.edit');
        $this->addJs('menu/menu.edit');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

    }

    function onAction_edit_metadata($id=null)
    {
        if (empty($id) || ($id < 0))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('menu/menu.edit.metadata');
        $this->addJs('menu/menu.edit.metadata');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

        $menu = $this->Menu;
        $menu_data = $menu->getMenu($id);

        if (empty($menu_data)) //$menu_data === false)
        {
            $this->redirect('/home/main');
            return;
        }

        $this->set('id', $id);
        $this->set('site', $menu_data['site']);
        $this->set('imgs', $menu_data['imgs']);
        $this->set('info', array());

        $menus = isset($_POST['menu']) ? $_POST['menu'] : array();
        //$menus = $this->parsePostStagingMenu($menus);
        //$menus = $this->validateStagingMenu($menus);
        $this->set('menus', $menus);

        $this->set('dbg',
            array(
                'post' => isset($_POST) ? $_POST : array(),
                //'menus' => $menus,
            )
        );
    }

}
