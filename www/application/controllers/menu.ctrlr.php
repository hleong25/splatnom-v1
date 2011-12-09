<?php

class MenuController
    extends Controller
{
    function onAction_new()
    {
        $this->addCss('menu/menu.new');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('menu/menu.new');

        if (!empty($_POST))
        {
            $this->set('new_menu_done', true);

            $urls = $_POST['url'];
            foreach ($urls as $idx => $url)
            {
                $url = Util::normalizeUrl($url);
                $urls[$idx] = $url;
            }

            $this->Menu->saveNewMenu($urls);
        }
    }

    function onAction_edit_metadata($id=null)
    {
        if (empty($id) || ($id < 0))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('menu/menu.edit.metadata');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('menu/menu.edit.metadata');

        $menu = $this->Menu;
        $menu_info = $menu->getMenuInfo($id);

        if (empty($menu_info))
        {
            Util::logit("No menu info for id: {$id}");
            $this->redirect('/home/main');
            return;
        }

        // it's in the database... let's continue

        $this->set('id', $id);
        $this->set('is_admin', Util::getPermissions('admin'));
        $this->set('is_metadata', Util::getPermissions('metadata'));

        $import_json = false;
        if (!empty($_FILES['import_file']['tmp_name']))
        {
            // if we're here, then there was a file uploaded...
            $file = $_FILES['import_file']['tmp_name'];
            $json_data = file_get_contents($file);
            $import_json = json_decode($json_data, true);

            $this->import_normalize($import_json);
        }

        if (empty($_POST))
        {
            $this->get_menu_metadata($id, $menu_info);
        }
        else
        {
            $this->edit_metadata_onPost($id, $menu_info, $import_json);
        }
    }

    function edit_metadata_onPost($id, $menu_info, $import_json)
    {
        $menu = $this->Menu;

        $imgs = $menu->getMenuImgs($id);
        $this->set('imgs', $imgs);

        $bImport_infos = false;
        $bImport_links = false;
        $bImport_menus = false;

        if (isset($_POST['import_file_opts']))
        {
            foreach ($_POST['import_file_opts'] as $import_opts)
            {
                switch ($import_opts)
                {
                    case 'infos':
                        $bImport_infos = true;
                        break;
                    case 'links':
                        $bImport_links = true;
                        break;
                    case 'menus':
                        $bImport_menus = true;
                        break;
                }
            }
        }

        $status = $_POST['info_status'];
        if (!$menu->updateMenu($id, $status))
            $err_msgs[] = 'Failed to update menu status.';

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

        $info_save = array
        (
            'name' => $_POST['info_name'],
            'notes' => $_POST['info_notes'],
            'address' => $_POST['info_address'],
            'latitude' => $_POST['info_latitude'],
            'longitude' => $_POST['info_longitude'],
            'numbers' => $_POST['info_numbers'],
            'hours' => $_POST['info_hours'],
        );

        if ($bImport_infos)
        {
            // override business info with the imported info
            $info_save = $import_json['info'];
        }

        // reuse menu_info['status'] and set the value
        $info = $info_save;
        foreach ($menu_info['status'] as $mi_status)
        {
            $info['status'][] = array(
                'status'=>$mi_status['status'],
                'selected'=> ($mi_status['status'] == $status) ? 1 : 0,
            );
        }

        $this->set('info', $info);
        if (!$menu->updateMenuInfo($id, $info_save))
            $err_msgs[] = 'Failed to update info.';

        $post_links = $_POST['link'];
        $links = array();
        $link = array();
        for ($ii = 0, $jj = count($post_links); $ii < $jj; $ii++)
        {
            switch ($post_links[$ii])
            {
                case '@link@':
                    $url = Util::normalizeUrl($post_links[++$ii]);
                    $label = $post_links[++$ii];

                    $link = array(
                        'url' => $url,
                        'label' => $label,
                    );

                    if (!empty($link['url']))
                    {
                        $links[] = $link;
                    }

                    break;
            }
        }

        if ($bImport_links)
        {
            $import_links = $import_json['links'];
            $links = array_merge($links, $import_links);
        }

        $this->set('links', $links);
        if (!$menu->updateMenuLinks($id, $links))
            $err_msgs[] = 'Failed to save menu links';

        $post_mdts = $_POST['mdt'];
        $mdts = array();
        $mdt = array();
        $ordinal_section = 0;
        $ordinal_metadata = 0;
        for ($ii = 0, $jj = count($post_mdts); $ii < $jj; $ii++)
        {
            switch ($post_mdts[$ii])
            {
                case '@mdt@':
                    $mdt = array(
                        'section_id' => $post_mdts[++$ii],
                        'ordinal' => $ordinal_section++,
                        'name' => $post_mdts[++$ii],
                        'notes' => $post_mdts[++$ii],
                    );
                    $ordinal_metadata = 0;
                    break;
                case '@item@':
                    $item = array(
                        'metadata_id' => $post_mdts[++$ii],
                        'ordinal' => $ordinal_metadata++,
                        'label' => $post_mdts[++$ii],
                        'price' => $post_mdts[++$ii],
                        'notes' => $post_mdts[++$ii],
                    );

                    // parse 'item' for easier input
                    // format is '{item}@@{price}@@{notes}
                    if (empty($item['price']) && empty($item['notes']))
                    {
                        $parsed = explode('@@', $item['label'], 3);

                        $item['label'] = trim(array_shift($parsed));
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

        if ($bImport_menus)
        {
            $import_mdts = $import_json['metadatas'];
            $mdts = array_merge($mdts, $import_mdts);
        }

        if (!$menu->updateMenuSectionAndMetadata($id, $mdts))
            $err_msgs[] = 'Failed to save menu data';

        // set the metadata only after it goes through updating...
        // that way we get the updated IDs
        $this->set('mdts', $mdts);
    }

    function get_menu_metadata($id, &$info)
    {
        $user_id = Util::getUserId();

        $menu = $this->Menu;
        $links = $menu->getMenuLinks($id);
        $imgs = $menu->getMenuImgs($id);
        $sections = $menu->getSection($id);
        $mdts = $menu->getMetadata($id, $sections);
        $forkits = $menu->getForkit($id, $user_id);

        $this->set('info', $info);
        $this->set('links', $links);
        $this->set('imgs', $imgs);

        if (!empty($mdts))
            $this->set('mdts', $mdts);

        if (!empty($forkits))
            $this->set('forkits', $forkits);
    }

    function onAction_purge($id)
    {
        if (empty($id) || ($id < 0) || !Util::getPermissions('admin'))
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

    function onAction_search($location=null, $query=null)
    {
        if (!empty($_POST))
        {
            global $get_url;

            $p_query = $_POST['query'];
            $p_location = $_POST['location'];

            $p_query = rawurlencode($p_query);

            $url = "/{$get_url}/{$p_location}/{$p_query}";

            $this->redirect($url);
            return;
        }

        $this->addJs('jquery.cookie', WEB_PATH_OTHER);
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

        $this->addCss('table');

        $this->set('location', $location);
        $this->set('query', $query);

        $loc = new LocationModel();
        $latlong = $loc->getLatLongByZip($location);
        if (empty($latlong))
        {
            $this->set('msg', 'No location found');
            return;
        }

        $places = $loc->getPlacesWithinLatLong($query, $latlong['latitude'], $latlong['longitude'], 10);
        if (empty($places))
        {
            $this->set('msg', 'No places found');
            return;
        }

        $this->set('places', $places);

    }

    function onAction_view($id=null)
    {
        if ($id == null)
        {
            $this->redirect('/home/main/');
            return;
        }

        $menu = $this->Menu;
        $menu_info = $menu->getMenuInfo($id);

        if (empty($menu_info))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addJqueryUi();

        $this->addJs('menu/menu.view');
        $this->addCss('menu/menu.view');
        $this->addCss('zebra');

        // it's in the database... let's continue

        $this->set('id', $id);
        $this->set('is_metadata', Util::getPermissions('metadata'));

        $this->get_menu_metadata($id, $menu_info);
    }

    function onAction_export($id=null,$out=null)
    {
        if ($id == null)
        {
            $this->redirect('/home/main/');
            return;
        }

        $menu = $this->Menu;
        $info = $menu->getMenuInfo($id);

        if (empty($info))
            return;

        $this->m_bRender = false;

        $sections = $menu->getSection($id);
        $links = $menu->getMenuLinks($id);
        $mdts = $menu->getMetadata($id, $sections);

        $export = array(
            'info' => $info,
            'links' => $links,
            'metadatas' => $mdts,
        );

        $this->export_normalize($export);

        $file = "menu.{$id}.txt";

        $this->set('export_data', $export);
        $this->set('out', $out);
        $this->set('file', $file);
    }

    function export_normalize(&$datas)
    {
        // clear status
        unset($datas['info']['status']);

        // clear the section_id and metadata_id
        foreach ($datas['metadatas'] as &$mtd)
        {
            $mtd['section_id'] = -1;

            foreach ($mtd['items'] as &$item)
            {
                $item['metadata_id'] = -1;
            }
        }
    }

    function onAction_import()
    {
        if (!Util::getPermissions('admin'))
        {
            $this->redirect('/home/main/');
            return;
        }

        if (empty($_POST))
        {
            return;
        }
        else if (empty($_FILES['import_file']['tmp_name']))
        {
            $this->set('err_msg', 'No file to import');
            return;
        }

        // if we're here, then there was a file uploaded...
        $file = $_FILES['import_file']['tmp_name'];
        $json_data = file_get_contents($file);
        $json = json_decode($json_data, true);

        $this->import_normalize($json);

        $menu = $this->Menu;
        $new_id = $menu->createMenu();

        // import info
        $info = $json['info'];
        $q_info = array(
            ':id' => $new_id,
            ':name' => $info['name'],
            ':notes' => $info['notes'],
            ':address' => $info['address'],
            ':latitude' => $info['latitude'],
            ':longitude' => $info['longitude'],
            ':numbers' => $info['numbers'],
            ':hours' => $info['hours'],
        );
        $import = $menu->updateMenuInfo($q_info);
        if (!$import)
        {
            $this->set('err_msg', 'Failed to import info');
            return;
        }

        // import links
        $links = $json['links'];
        $q_links = &$links;
        $import = $menu->updateMenuLinks($new_id, $q_links);
        if (!$import)
        {
            $this->set('err_msg', 'Failed to import links');
            return;
        }

        // import metadata
        $mdts = $json['metadatas'];
        $q_mdts = &$mdts;
        $import = $menu->updateMenuSectionAndMetadata($new_id, $q_mdts);
        if (!$import)
        {
            $this->set('err_msg', 'Failed to import menu metadata');
            return;
        }

        // create menu image directory
        $menu_img_path = OS_MENU_PATH . DS . $new_id;
        if (mkdir($menu_img_path) == false)
        {
            $err_msg = "Failed to create menu directory: {$menu_img_path}";
            Util::logit($err_msg, __FILE__, __LINE__);
            $this->set('err_msg', $err_msg);
            return false;
        }

        $this->set('menu_id', $new_id);
    }

    function import_normalize(&$datas)
    {
        $ordinal_section = 0;
        $ordinal_item = 0;

        // make sure section_id and metadata_id are -1
        foreach ($datas['metadatas'] as &$mtd)
        {
            $mtd['section_id'] = -1;
            $mtd['ordinal'] = $ordinal_section++;

            $ordinal_item = 0;
            foreach ($mtd['items'] as &$item)
            {
                $item['metadata_id'] = -1;
                $item['ordinal'] = $ordinal_item++;
            }
        }
    }

    function onAction_forkit($menu_id, $section_id, $item_id)
    {
        $this->m_bRender = false;
        $data = array();

        $user_id = Util::getUserId();
        $menu = $this->Menu;

        if (!$user_id)
        {
            $data['status'] = 'error';
            $data['msg'] = 'Not logged in';
        }
        else if (!$menu->isValidMetadataId($menu_id, $section_id, $item_id))
        {
            $data['status'] = 'error';
            $data['msg'] = 'Not valid';
        }
        else
        {
            $forkit = $menu->forkit($user_id, $menu_id, $section_id, $item_id);
            if (!$forkit)
            {
                $data['status'] = 'error';
                $data['msg'] = 'Failed to fork it!';
            }
            else
            {
                $data['status'] = 'forkit';
            }
        }

        $this->set('data', $data);
    }

    function onAction_unforkit($menu_id, $section_id, $item_id)
    {
        $this->m_bRender = false;
        $data = array();

        $user_id = Util::getUserId();
        $menu = $this->Menu;

        if (!$user_id)
        {
            $data['status'] = 'error';
            $data['msg'] = 'Not logged in';
        }
        else if (!$menu->isValidMetadataId($menu_id, $section_id, $item_id))
        {
            $data['status'] = 'error';
            $data['msg'] = 'Not valid';
        }
        else
        {
            $forkit = $menu->unforkit($user_id, $menu_id, $section_id, $item_id);
            if (!$forkit)
            {
                $data['status'] = 'error';
                $data['msg'] = 'Failed to unfork it!';
            }
            else
            {
                $data['status'] = 'unforkit';
            }
        }

        $this->set('data', $data);
    }

    function onAction_item($menu_id=null, $section_id=null, $metadata_id=null)
    {
        $menu = $this->Menu;
        $item = $menu->getMenuItem($menu_id, $section_id, $metadata_id);
        if (empty($item))
        {
            $this->redirect("/menu/view/{$menu_id}");
            return;
        }

        $this->addCss('menu/menu.item');

        $this->set('menu_id', $menu_id);
        $this->set('item', $item);
    }
}
