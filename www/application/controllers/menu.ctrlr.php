<?php

class MenuController
    extends Controller
{
    function onAction_new()
    {
        $this->addCss('menu/menu.new');

        $this->addJqueryUi();
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('menu/menu.new');

        $user_id = Util::getUserId();
        $this->set('is_user', !empty($user_id));

        if (!empty($_POST))
        {
            $link_cnt = 0;
            $urls = $_POST['url'];
            foreach ($urls as $idx => $url)
            {
                $url = Util::normalizeUrl($url);
                $urls[$idx] = $url;

                if (!empty($url))
                    $link_cnt++;
            }

            $files_cnt = 0;
            if (isset($_FILES['imgs']))
            {
                foreach ($_FILES['imgs']['tmp_name'] as $idx => $file)
                {
                    if (!empty($file))
                        $files_cnt++;
                }
            }

            if ($link_cnt > 0 || $files_cnt > 0)
            {
                $this->set('new_menu_done', true);
                $this->Menu->saveNewMenu($urls);
            }
            else
            {
                $this->set('err_msg', 'No menu submited!');
            }
        }
    }

    function onAction_edit_metadata($id=null)
    {
        $user_id = Util::getUserId();
        $bPermMdt = Util::getPermissions('metadata');
        if (empty($id) || ($id < 0) || empty($user_id) || empty($bPermMdt))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('menu/menu.edit.metadata');

        $this->addJqueryUi();
        $this->addJs('jquery.tmpl.min', WEB_PATH_OTHER);
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

        /*
        NOTE: the import function is no longer supported here
              it will be supported in the /import/list section
        if (!empty($_FILES['import_file']['tmp_name']))
        {
            // if we're here, then there was a file uploaded...
            $file = $_FILES['import_file']['tmp_name'];
            $json_data = file_get_contents($file);
            $import_json = json_decode($json_data, true);

            $this->import_normalize($import_json);
        }
        */

        $load_from_db = empty($_POST);

        if (!$load_from_db)
        {
            $this->edit_metadata_onPost($id, $menu_info, $import_json);

            $load_from_db = !empty($_POST['force_reload']);
        }

        if ($load_from_db)
        {
            $this->get_menu_metadata($id, $menu_info);
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

        $info['total_items'] = -1;

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

        // cache metadata ids to check for duplicate ids
        $mids = array();

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
                        'name' => trim($post_mdts[++$ii]),
                        'notes' => trim($post_mdts[++$ii]),
                    );
                    $ordinal_metadata = 0;

                    // clear metadata cache
                    $mids = array();

                    break;
                case '@item@':
                    $item = array(
                        'metadata_id' => $post_mdts[++$ii],
                        'ordinal' => $ordinal_metadata++,
                        'label' => trim($post_mdts[++$ii]),
                        'price' => trim($post_mdts[++$ii]),
                        'notes' => trim($post_mdts[++$ii]),
                        'is_spicy' => false,
                    );

                    $post_mid = $item['metadata_id'];
                    if (!empty($post_mid))
                    {
                        if (in_array($post_mid, $mids))
                        {
                            Util::logit("Dupliate metadata id. menu({$id}) section({$mdt['section_id']}) metadata({$post_mid}) ordinal({$item['ordinal']})", __FILE__, __LINE__);

                            // clear it since it's probably a JS error
                            $item['metadata_id'] = '';
                        }
                        else
                        {
                            $mids[] = $post_mid;
                        }
                    }

                    // parse 'item' for easier input
                    // format is '{item}@@{price}@@{notes}@@{attrs}
                    if (empty($item['price']) && empty($item['notes']))
                    {
                        $parsed = explode('@@', $item['label'], 4);

                        $item['label'] = trim(array_shift($parsed));
                        $item['price'] = trim(array_shift($parsed));
                        $item['notes'] = trim(array_shift($parsed));

                        $attrs = trim(array_shift($parsed));
                        $attrs_len = strlen($attrs);
                        for ($attrs_idx = 0; $attrs_idx < $attrs_len; $attrs_idx++)
                        {
                            if ($attrs[$attrs_idx] === 'S')
                                $item['is_spicy'] = true;
                        }
                    }

                    $mdt['items'][] = $item;
                    break;
                case '@item_attr@':
                    $item = array_pop($mdt['items']);

                    $is_spicy = false;

                    if (($post_mdts[++$ii] === 'is_spicy') && ($post_mdts[$ii + 1] === 'on'))
                    {
                        ++$ii;
                        $item['is_spicy'] = true;
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

        // recount the mdt items and set the info property
        $cnt_mdt_items = 0;
        foreach ($mdts as $mdt)
            $cnt_mdt_items += count($mdt['items']);

        $info['total_items'] = $cnt_mdt_items;
        $this->set('info', $info);

        // set the metadata only after it goes through updating...
        // that way we get the updated IDs
        $this->set('mdts', $mdts);
        //$this->set('dbg', $mdts);
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
        /*
            NOTE: mod_rewrite URL change in .htaccess
            original URL request is in $_GET format, after mod_rewrite it changes to MVC model
        */

        $this->addCss('menu/menu.search');

        $this->addJqueryUi();
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('menu/menu.search');

        $this->set('location', $location);
        $this->set('query', $query);

        $loc = new LocationModel();
        $latlong = $loc->getLatLongByZip($location);
        if (empty($latlong))
        {
            $this->set('msg', 'Location not valid');
            return;
        }
        else
        {
            // valid location... lets set the cookie
            Util::cookie('location', $location);
        }

        $this->set('msg', "Searching for: {$query}");

        $places = $loc->getPlacesWithinLatLong($query, $latlong['latitude'], $latlong['longitude'], 10);
        if (empty($places))
        {
            $this->set('msg', 'No places found');
            return;
        }

        $max_score = -1;
        foreach ($places as &$place)
        {
            if ($place['score'] > $max_score)
                $max_score = $place['score'];

            $place['score'] /= $max_score;
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

        $name = $menu_info['name'];
        $this->set('meta_title', $name);
        $this->set('meta_desc', "Delicious food at $name... mmMmmmMmmm nom nom nom says Zoidberg! (\/)(',,,,')(\/)");

        $this->addJqueryUi();

        $this->addJs('menu/menu.view');
        $this->addCss('menu/menu.view');

        // it's in the database... let's continue

        $this->set('id', $id);
        $this->set('is_metadata', Util::getPermissions('metadata'));

        $this->get_menu_metadata($id, $menu_info);
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

    function onAction_images1($menu_id=null, $section_id=null, $item_id=null, $view_img=null)
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

        $id_names = $menu->getIdAndNames($menu_id, $section_id, $item_id);

        if (empty($menu_info) || empty($id_names))
        {
            $this->redirect("/menu/view/{$menu_id}");
            return;
        }

        $this->addCss('menu/menu.images');

        $this->addJqueryUi();
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('menu/menu.images');

        $user_id = Util::getUserId();
        $this->set('is_logged_in', $user_id !== false);

        $this->set('menu_id', $id_names['menu_id']);
        $this->set('section_id', $id_names['section_id']);
        $this->set('item_id', $id_names['item_id']);

        $this->set('menu_str', $id_names['menu']);
        $this->set('section_str', $id_names['section']);
        $this->set('item_str', $id_names['item']);

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
            $selected_img = @$menu_imgs[0];

        $this->set('selected_img', $selected_img);

        $taggits = $menu->getTaggitsByImageFile($menu_id, $selected_img['filename']);
        $this->set('taggits', $taggits);

        $tags = $menu->getMenuTags($menu_id);
        $this->set('tags', $tags);
    }

    function onAction_taggit($type=null, $menu_id=null, $p1=null)
    {
        switch ($type)
        {
            case 'images':
                $this->taggit_images($menu_id, $p1);
                break;
            default:
                $this->redirect('/home/main/');
        }
    }

    function taggit_images($menu_id=null, $view_img=null)
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
            $err_msg  = "Taggit images for menu_id({$menu_id}) has wrong count. ";
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
            $this->Menu->updateTaggitsImage($menu_id, $view_img, $add_taggits, $remove_taggits);
        }

        $this->redirect($backurl);
    }

    function onAction_comments($menu_id=null, $section_id=null, $item_id=null)
    {
        if ($menu_id == null)
        {
            $this->redirect('/home/main/');
            return;
        }

        $menu = $this->Menu;
        $menu_info = $menu->getMenuInfo($menu_id);

        $id_names = $menu->getIdAndNames($menu_id, $section_id, $item_id);

        $this->addJqueryUi();
        $this->addJs('menu/menu.comments');

        $this->addCss('menu/menu.comments');

        $this->set('menu_id', $id_names['menu_id']);
        $this->set('section_id', $id_names['section_id']);
        $this->set('item_id', $id_names['item_id']);

        $this->set('menu_str', $id_names['menu']);
        $this->set('section_str', $id_names['section']);
        $this->set('item_str', $id_names['item']);

        $comments = array();
        if (!empty($menu_id))
        {
            if (empty($section_id))
            {
                $comments = $menu->getMenuComments($menu_id);
            }
            else
            {
                if (empty($item_id))
                    $comments = $menu->getMenuSectionComments($menu_id, $section_id);
                else
                    $comments = $menu->getMenuItemComments($menu_id, $section_id, $item_id);
            }
        }

        $this->set('comments', $comments);

        $comment_tags = $menu->getTaggitsCommentByMenuId($menu_id);

        $taggits = array();
        foreach ($comment_tags as $tag)
        {
            $comment_id = $tag['comment_id'];
            $taggits[$comment_id][] = $tag;
        }
        $this->set('taggits', $taggits);
    }

    function onAction_edit_comments($menu_id=null, $section_id=null, $item_id=null)
    {
        if ($menu_id == null)
        {
            $this->redirect('/home/main/');
            return;
        }

        $user_id = Util::getUserId();

        if (empty($user_id))
        {
            $this->redirect("/menu/view/{$menu_id}");
            return;
        }

        $menu = $this->Menu;
        $menu_info = $menu->getMenuInfo($menu_id);

        if (empty($menu_id))
        {
            $this->redirect('/home/main/');
            return;
        }

        $this->addJqueryUi();
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('menu/menu.edit.comments');

        $this->addCss('menu/menu.edit.comments');

        $id_names = $menu->getIdAndNames($menu_id, $section_id, $item_id);

        $this->set('menu_id', $id_names['menu_id']);
        $this->set('section_id', $id_names['section_id']);
        $this->set('item_id', $id_names['item_id']);

        $this->set('menu_str', $id_names['menu']);
        $this->set('section_str', $id_names['section']);
        $this->set('item_str', $id_names['item']);

        $tags = $menu->getMenuTags($menu_id);
        $this->set('tags', $tags);

        if (!empty($id_names['section_id']) && !empty($id_names['item_id']))
        {
            $taggits[] = array
            (
                'sid'=>$id_names['section_id'],
                'section'=>$id_names['section'],
                'mid'=>$id_names['item_id'],
                'metadata'=>$id_names['item'],
            );
            $this->set('taggits', $taggits);
        }

        if (empty($_POST))
        {
            return;
        }

        $post_mid = $_POST['menu_id'];
        $post_cid = $_POST['cid'];
        $post_comments = $_POST['comments'];

        $this->set('post_comments', $post_comments);

        // validate checks...
        if ($post_mid != $menu_id)
        {
            // shouldn't be here... but if it does, log it
            Util::logit("Edit comment post_mid({$post_mid}) != menu_id({$menu_id})", __FILE__, __LINE__);
            $this->set('err_msg', 'Failed to add comment.');
            return;
        }

        $img_id = 0;
        if (!empty($_FILES))
        {
            $path = OS_MENU_PATH . DS . $menu_id;
            $imgs = Util::handle_upload_files($path);

            if (empty($imgs))
            {
                // TODO: check for failed image upload
                // $this->set('is_err', true);
                // return;
            }

            $insertImgs = $menu->insertMenuImages($menu_id, $user_id, $imgs);

            if (!empty($insertImgs))
                $img_id = $insertImgs[0]['img_id'];
        }

        // TODO: if it's editing the comments, then it needs a way to not override the current img_id

        // finally... lets add/modify the comment
        $comment_id = $menu->updateMenuComments($post_cid, $menu_id, $user_id, $img_id, $post_comments);
        if (empty($comment_id))
        {
            $this->set('err_msg', 'Failed to add comment.');
            return;
        }

        $this->set('comment_id', $comment_id);

        // handle taggits!
        $adds = isset($_POST['add']) ? $_POST['add'] : array();
        $sids = isset($_POST['sid']) ? $_POST['sid'] : array();
        $mids = isset($_POST['mid']) ? $_POST['mid'] : array();

        if (count($sids) != count($mids))
        {
            $err_msg  = "Taggit comments for menu_id({$menu_id}) has wrong count. ";
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

            // this is for the template...
            if (empty($mid) || empty($sid))
                continue;

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
            $this->Menu->updateTaggitsComment($menu_id, $comment_id, $add_taggits, $remove_taggits);
        }

        $taggits = $menu->getTaggitsByCommentId($menu_id, $comment_id);
        $this->set('taggits', $taggits);
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

        $id_names = $menu->getIdAndNames($menu_id, $section_id, $item_id);

        if (empty($menu_info) || empty($id_names))
        {
            $this->redirect("/menu/view/{$menu_id}");
            return;
        }

        $this->addJs('menu/menu.images');
        $this->addCss('menu/menu.images');

        $this->set('info', $menu_info);
        $this->set('id_names', $id_names);

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

            // set preview/thumbnail info
            $preview_size = 'sm';
            $preview_preferred_size = ImagesModel::getPreferredSize($preview_size);

            foreach ($menu_imgs as &$img)
            {
                $new_size = ImageresizeUtil::resizeDimension
                (
                    $img['width'], $img['height'],
                    $preview_preferred_size['width'], $preview_preferred_size['height']
                );

                $img['preview'] = array(
                    'size' => $preview_size,
                    'width' => $new_size['width'],
                    'height' => $new_size['height'],
                );
            }
        }

        $this->set('imgs', $menu_imgs);

        $selected_img = $view_img;
        if (!empty($selected_img))
            $selected_img = $menu->getImgFromArray($selected_img, $menu_imgs);

        if (empty($selected_img))
            $selected_img = @$menu_imgs[0];

        if (!empty($selected_img))
        {
            // if the selected image is not empty, set the preview info
            $preview_size = 'lg';
            $preview_preferred_size = ImagesModel::getPreferredSize($preview_size);

            $new_size = ImageresizeUtil::resizeDimension
            (
                $selected_img['width'], $selected_img['height'],
                $preview_preferred_size['width'], $preview_preferred_size['height']
            );

            $selected_img['preview'] = array(
                'size' => $preview_size,
                'width' => $new_size['width'],
                'height' => $new_size['height'],
            );
        }

        $this->set('selected_img', $selected_img);

        $taggits = $menu->getTaggitsByImageFile($menu_id, $selected_img['filename']);
        $this->set('taggits', $taggits);

        $tags = $menu->getMenuTags($menu_id);
        $this->set('tags', $tags);
    }
}
