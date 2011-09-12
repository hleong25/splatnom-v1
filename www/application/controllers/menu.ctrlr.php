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

        if (empty($menu_data))
        {
            $this->redirect('/home/main');
            return;
        }

        $err_msgs = array();

        $this->set('id', $id);
        $this->set('site', $menu_data['site']);
        $this->set('imgs', $menu_data['imgs']);

        $info_params = array(
                'info_name' => 'name',
                'info_addy1' => 'addy1',
                'info_addy2' => 'addy2',
                'info_city' => 'city',
                'info_state' => 'state',
                'info_zip' => 'zip',
                'info_numbers' => 'numbers',
                'info_hours' => 'hours',
            );

        if (!empty($_POST))
        {
            // business info
            $info = array('id'=>$id);
            foreach ($info_params as $post_key => $sql_key)
            {
                if (!isset($_POST[$post_key]))
                    continue;

                $val = $_POST[$post_key];

                $this->set($post_key, $val);

                $info[":{$sql_key}"] = $val;
                $info[":u_{$sql_key}"] = $val;
            }

            if (!$this->Menu->updateMenuInfo($info))
                $err_msgs[] = 'Failed to update info.';
        }
        else
        {
            $info = $this->Menu->getMenuInfo($id);
            foreach ($info_params as $post_key => $sql_key)
            {
                if (!isset($info[$sql_key]))
                    continue;

                $this->set($post_key, $info[$sql_key]);
            }
        }

        $menus = isset($_POST['menu']) ? $_POST['menu'] : array();
        //$menus = $this->parsePostStagingMenu($menus);
        //$menus = $this->validateStagingMenu($menus);
        $this->set('menus', $menus);

        $this->set('err_msgs', $err_msgs);

        $this->set('dbg',
            array(
                'post' => isset($_POST) ? $_POST : array(),
                //'menus' => $menus,
            )
        );
    }

    function onAction_purge($id)
    {
        if (empty($id) || ($id < 0) || !getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }

        if ($this->Menu->purgeMenu($id))
        {
            $this->redirect('/home/main');
            return;
        }
        else
        {
            $this->redirect('/menu/edit_metadata/'.$id);
            return;
        }
    }

}
