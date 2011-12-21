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

        $this->addJqueryUi();
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
            'version' => 1,
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
        $q_info = &$info;

        $import = $menu->updateMenuInfo($new_id, $q_info);
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

    function onAction_upload($menu_id=null)
    {
        $this->m_bRender = false;

        if (empty($menu_id))
        {
            // nothing to do here...
            Util::logit('No menu id identified for upload');
            return;
        }

    }

    function onAction_images($menu_id=null, $section_id=null, $item_id=null, $view_img=null)
    {
        if ($menu_id == null)
        {
            $this->redirect('/home/main/');
            return;
        }

        $argc = func_num_args();
        $args = func_get_args();
        $last_arg = $args[$argc-1];

        // special handler for selected viewing image
        // 1. check if the last argument is view_img
        // 2. if not, then parse last argument to see if it's supposed to be view_img
        // 3. if it is a valid view_img, then set it
        // 4. clean up the variables
        if ($last_arg != $view_img)
        {
            $decode = Util::decodeUniqueString($last_arg);
            if (!empty($decode))
            {
                $view_img = $last_arg;

                // clean up...
                switch ($argc)
                {
                    case 2:
                        $section_id = null;
                        break;
                    case 3:
                        $item_id = null;
                        break;
                }
            }
        }

        $menu = $this->Menu;
        $menu_info = $menu->getMenuInfo($menu_id);

        if (empty($menu_info))
        {
            $this->redirect("/menu/view/{$menu_id}");
            return;
        }

        $this->addCss('menu/menu.images');

        $this->addJqueryUi();
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('menu/menu.images');

        $this->set('id', $menu_id);
        $this->set('info', $menu_info);

        $menu_imgs = array();

        if (!empty($menu_id))
        {
            if (empty($section_id))
            {
                $menu_imgs = $menu->getMenuImgs($menu_id);
            }
            else
            {
                if (empty($item_id))
                    $menu_imgs = $menu->getMenuSectionImgs($menu_id, $section_id);
                else
                    $menu_imgs = $menu->getMenuItemImgs($menu_id, $section_id, $item_id);
            }
        }

        $this->set('imgs', $menu_imgs);

        $selected_img = $view_img;
        if (!empty($selected_img))
            $selected_img = $menu->getImgFromArray($selected_img, $menu_imgs);

        if (empty($selected_img))
            $selected_img = $menu_imgs[0];

        $this->set('selected_img', $selected_img);

        $taggits = $menu->getTaggitsByImageFile($menu_id, $selected_img['filename']);
        $this->set('taggits', $taggits);

        $tags = $menu->getMenuTags($menu_id);
        $this->set('tags', $tags);
    }

    function onAction_taggit($menu_id=null, $view_img=null)
    {
        if ($menu_id == null)
        {
            $this->redirect('/home/main/');
            return;
        }

        if ($view_img == null)
        {
            $this->redirect("/menu/view/{$menu_id}");
            return;
        }

        $backurl = "/menu/view/{$menu_id}";
        if (isset($_POST['backurl']))
            $backurl = "/{$_POST['backurl']}";

        $adds = isset($_POST['add']) ? $_POST['add'] : array();
        $sids = isset($_POST['sid']) ? $_POST['sid'] : array();
        $mids = isset($_POST['mid']) ? $_POST['mid'] : array();

        if (count($sids) != count($mids))
        {
            $err_msg  = "Taggits for menu_id({$menu_id}) has wrong count. ";
            $err_msg .= 'sids=>'.var_export($sids, true).' ';
            $err_msg .= 'mids=>'.var_export($mids, true);

            Util::logit($err_msg, __FILE__, __LINE__);
            $this->redirect($backurl);
            return;
        }

        $add_taggits = array();
        $remove_taggits = array();

        foreach ($mids as $idx => $mid)
        {
            $sid = $sids[$idx];
            $taggit = array
            (
                'sid' => $sid,
                'mid' => $mid,
            );

            if (!empty($adds[$idx]))
                $add_taggits[] = $taggit;
            else
                $remove_taggits[] = $taggit;
        }

        if ((count($add_taggits) > 0) || (count($remove_taggits) > 0))
        {
            $this->Menu->updateTaggits($menu_id, $view_img, $add_taggits, $remove_taggits);
        }

        $this->redirect($backurl);
    }
}
