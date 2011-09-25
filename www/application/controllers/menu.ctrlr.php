<?php

class MenuController
    extends Controller
{
    function onAction_new()
    {
        $this->addCss('menu/menu.new');
        $this->addJs('menu/menu.new');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

        if (!empty($_POST))
        {
            $this->set('new_menu_done', true);

            $urls = $_POST['url'];
            foreach ($urls as $idx => $url)
            {
                $url = UtilsModel::normalizeUrl($url);
                $urls[$idx] = $url;
            }

            $this->Menu->saveNewMenu($urls);
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
        $menu_info = $menu->getMenuInfo($id);

        if (empty($menu_info))
        {
            $this->redirect('/home/main');
            return;
        }

        // it's in the database... let's continue

        $this->set('id', $id);
        $this->set('is_admin', UtilsModel::getPermissions('admin'));

        if (empty($_POST))
        {
            $this->edit_metadata_onInit($id, $menu_info);
        }
        else
        {
            $this->edit_metadata_onPost($id);
        }
    }

    function edit_metadata_onPost($id)
    {
        $menu = $this->Menu;

        $info_params = array(
                'info_name' => 'name',
                'info_notes' => 'notes',
                'info_address' => 'address',
                'info_latitude' => 'latitude',
                'info_longitude' => 'longitude',
                'info_numbers' => 'numbers',
                'info_hours' => 'hours',
            );

        // parse lat/long if needed
        if (!empty($_POST['info_latitude']) && empty($_POST['info_longitude']))
        {
            $latlong = $_POST['info_latitude'];

            $lat = 0;
            $long = 0;

            $n = sscanf($latlong, '(%f, %f)', $lat, $long);

            if ($n === 2)
            {
                $_POST['info_latitude'] = $lat;
                $_POST['info_longitude'] = $long;
            }
        }

        $info = array();
        $info_save = array('id'=>$id);
        foreach ($info_params as $post_key => $sql_key)
        {
            if (!isset($_POST[$post_key]))
                continue;

            $val = $_POST[$post_key];

            $this->set($post_key, $val);

            $info[$sql_key] = $val;
            $info_save[":{$sql_key}"] = $val;
            $info_save[":u_{$sql_key}"] = $val;
        }

        $info['site_addy'] = 'www.not_done.com';

        $this->set('info', $info);
        if (!$menu->updateMenuInfo($info_save))
            $err_msgs[] = 'Failed to update info.';

        $imgs = $menu->getMenuImgs($id);
        $this->set('imgs', $imgs);

        $post_mdts = $_POST['mdt'];

        $mdts = array();
        $mdt = array();

        for ($ii = 0, $jj = count($post_mdts); $ii < $jj; $ii++)
        {
            switch ($post_mdts[$ii])
            {
                case '@mdt@':
                    $mdt = array(
                        'name' => $post_mdts[++$ii],
                        'notes' => $post_mdts[++$ii],
                    );
                    break;
                case '@item@':
                    $item = array(
                        'item' => $post_mdts[++$ii],
                        'price' => $post_mdts[++$ii],
                        'notes' => $post_mdts[++$ii],
                    );

                    // parse 'item' for easier input
                    // format is '{item}@@{price}@@{notes}
                    if (empty($item['price']) && empty($item['notes']))
                    {
                        $parsed = explode('@@', $item['item'], 3);

                        $item['item'] = trim(array_shift($parsed));
                        $item['price'] = trim(array_shift($parsed));
                        $item['notes'] = trim(array_shift($parsed));
                    }

                    $mdt['items'][] = $item;
                    break;
                case '@end_of_mdt@':
                    $mdts[] = $mdt;
                    break;
            }
        }

        $this->set('mdts', $mdts);
        if (!$menu->updateMenuSectionAndMetadata($id, $mdts))
            $err_msgs[] = 'Failed to save menu data';

    }

    function edit_metadata_onInit($id, &$info)
    {
        $menu = $this->Menu;
        $links = $menu->getMenuLinks($id);
        $imgs = $menu->getMenuImgs($id);
        $sections = $menu->getSection($id);
        $mdts = $menu->getMetadata($id, $sections);

        $this->set('info', $info);
        $this->set('links', $links);
        $this->set('imgs', $imgs);

        if (!empty($mdts))
            $this->set('mdts', $mdts);

    }

    function onAction_purge($id)
    {
        if (empty($id) || ($id < 0) || !UtilsModel::getPermissions('admin'))
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
