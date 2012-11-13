<?php

class AdminController
    extends Controller
{
    function onAction_main()
    {
        $this->addCss('admin/admin.main');

        $menu = new MenuModel();

        $this->set('cnt_pending_menus', $menu->getPendingMenuCount());
    }

    function onAction_pendingmenu_list($action = null, $id = null)
    {
        $menu = new MenuModel();

        if (!empty($action) && (!empty($id)))
        {
            if ($action === 'purge')
            {
                $bPurged = $menu->purgePendingMenu($id);
            }
        }

        $this->addCss('admin/admin.pendingmenu.list');

        $this->addJs('admin/admin.pendingmenu.list');

        $this->set('new_menus', $menu->getPendingMenus());
    }

    function onAction_find_user()
    {
        $this->addCss('admin/admin.find.user');

        $this->addJs('new.jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('admin/admin.find.user');

        $this->set('query_user', '%');

        if (empty($_GET) || empty($_GET['query_user']))
            return;

        $query_user = $_GET['query_user'];
        $this->set('query_user', $query_user);

        $user_search = $this->Admin->getUserSearch($query_user);

        $this->set('query_result', $user_search);
    }

    function onAction_user_profile($id = null)
    {
        if (empty($id) || ($id < 0))
        {
            $this->redirect('/admin/find_user');
            return;
        }

        $this->addCss('admin/admin.user.profile');

        $user = new UserModel();

        if (!empty($_POST))
        {
            $settings = array();
            $settings['permissions'] = array();

            if (!empty($_POST['perms']))
                $settings['permissions'] = $_POST['perms'];

            $this->handle_profile_update($user, $id, $settings);

            if ($id === Util::getUserId())
            {
                Util::clearPermissions();
            }
        }

        $info = $user->getUser($id);
        if (empty($info))
        {
            $this->redirect('/admin/find_user');
            return;
        }

        $permissions = $user->getUserPermission($id);

        $this->set('info', $info);
        $this->set('permissions', $permissions);
    }

    function handle_profile_update($user, $user_id, $settings)
    {
        $user->setUserPermissions($user_id, $settings['permissions']);
    }

    function onAction_pending_menu($id = null)
    {
        if (empty($id) || ($id < 0))
        {
            $this->redirect('/admin/pendingmenu_list');
            return;
        }

        $this->addCss('admin/admin.pending.menu');
        $this->addJs('new.jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('admin/admin.pending.menu');

        $menu = new MenuModel();
        $menu_data = $menu->getPendingMenu($id);

        if ($menu_data === false)
        {
            $this->redirect('/admin/pendingmenu_list');
            return;
        }

        $this->set('pending_id', $id);
        $this->set('err_msg', empty($_GET['err_msg']) ? '' : $_GET['err_msg']);
        $this->set('sites', $menu_data['sites']);
        $this->set('imgs', $menu_data['imgs']);
        $this->set('search_arg', array('name'=>'', 'location'=>''));

        if (empty($_POST['action']))
            return;

        switch ($_POST['action'])
        {
            case 'search':
                $q_name = $_POST['name'];
                $q_location = $_POST['location'];

                $this->set('search_arg', array('name'=>$q_name, 'location'=>$q_location));

                $loc = new LocationModel();
                $latlong = $loc->getLatLongByZip($q_location);
                if (empty($latlong))
                {
                    $this->set('search_msg', 'No location found');
                    break;
                }

                $places = $loc->getPlacesWithinLatLong($q_name, $latlong['latitude'], $latlong['longitude'], 10);
                if (empty($places))
                {
                    $this->set('search_msg', 'No places found');
                    break;
                }

                $this->set('search_rst', $places);

                break;
        }

    }

    function onAction_transfer_menu($id = null)
    {
        $this->m_bRender = false;

        if (empty($id) || ($id < 0))
        {
            $this->redirect('/admin/pendingmenu_list');
            return;
        }

        $menu = new MenuModel();
        $new_menu = $menu->pendingMenuApproved($id);
        if ($new_menu === false)
        {
            $msg = '&err_msg=failed';
            $this->redirect('/admin/pending_menu/'.$id.'/'.$msg);
            return;
        }
        else
        {
            $this->redirect('/menu/edit_metadata/'.$new_menu);
            return;
        }
    }

    function onAction_location($opt=null)
    {
        $this->addCss('admin/admin.location');

        $this->addJs('new.jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('admin/admin.location');

        $param = false;

        if (empty($_GET))
            return;

        $param = $_GET;

        $loc = new LocationModel();

        switch ($opt)
        {
            case 'gmap':
                $query = $param['query'];
                $this->set('q_gmap_query', $query);

                $cached_latlong = $loc->getCachedLatLong($query, true);

                if (!empty($cached_latlong['details']))
                {
                    //$this->set('dbg', $cached_latlong['details']);
                    $this->set('map_request', @$cached_latlong['details']['json']['results'][0]['locations']);
                }

                if (!$cached_latlong['status'])
                {
                    break;
                }

                $latlong = $cached_latlong['coords'];

                $lat = $latlong['latitude'];
                $long = $latlong['longitude'];

                $this->set('q_lat', $lat);
                $this->set('q_long', $long);

                break;

//            case 'zip':
//                $zip = $param['zip'];
//                $this->set('q_zip', $zip);
//
//                $latlong = $loc->getLatLongByZip($zip);
//
//                if (!$latlong)
//                    break;
//
//                $lat = $latlong['latitude'];
//                $long = $latlong['longitude'];
//
//                $this->set('q_lat', $lat);
//                $this->set('q_long', $long);
//
//                break;
//            case 'citystate':
//                $citystate = $param['citystate'];
//                $this->set('q_citystate', $citystate);
//
//                $citystate = $loc->parseCityState($citystate);
//                if (!$citystate)
//                    break;
//
//                $city = $citystate['city'];
//                $state = $citystate['state'];
//
//                $latlong = $loc->getLatLongByCityState($city, $state);
//
//                if (!$latlong)
//                    break;
//
//                $lat = $latlong['latitude'];
//                $long = $latlong['longitude'];
//
//                $this->set('q_lat', $lat);
//                $this->set('q_long', $long);
//
//                break;
//            case 'address':
//                $address = $param['address'];
//                $this->set('q_address', $address);
//
//                $filter = explode(',', $address);
//                foreach ($filter as &$key)
//                {
//                    $key = trim($key);
//                }
//
//                $locations = $loc->getLocationsByAddress($address, $filter);
//                $this->set('found_locations', $locations);
//
//                break;
//
            case 'latlong':
                $lat = $param['lat'];
                $long = $param['long'];
                $radius = $param['radius'];

                $this->set('q_lat', $lat);
                $this->set('q_long', $long);
                $this->set('q_radius', $radius);

                $nearby = $loc->getLocationsWithinLatLong($lat, $long, $radius);
                $this->set('nearby_query', $nearby);

                break;
        }
    }

    function onAction_jqueryui_test()
    {
        $this->redirect('/public/other/jquery-ui/jquery-ui-1.8.24.custom');
    }

    function onAction_update_css()
    {
        $this->addCss('admin/admin.updatecss');
        $this->addJqueryUi();
        $this->addJs('admin/admin.update.css');

        // ls -Rl | grep cache
        $allCss = array(
            'admin/admin.email.queue',
            'admin/admin.find.user',
            'admin/admin.location',
            'admin/admin.main',
            'admin/admin.pending.menu',
            'admin/admin.pendingmenu.list',
            'admin/admin.updatecss',
            'admin/admin.user.profile',
            'export/export.list',
            'home/home.about',
            'home/home.feedback',
            'home/home.gmapmenu',
            'home/home.main',
            'images/images.upload',
            'import/import.list',
            'import/import.local',
            'import/import.menus',
            'login/login.forgot',
            'login/login.main',
            'login/login.reset',
            'mail/mail.sent',
            'mail/mail.tester',
            'menu/menu.edit.metadata',
            'menu/menu.images',
            'menu/menu.new',
            'menu/menu.search',
            'menu/menu.view',
            'user/user.profile',
            'user/user.register',
            'user/user.verify',
        );

        if (!empty($_POST))
        {
            $template = $this->m_template;

            // auto update the css
            $update_css = $_POST['css'];

            foreach ($update_css as $css)
            {
                $template->auto_compile_less($css);
            }
        }

        $lstCss = array();

        foreach ($allCss as $css)
        {
            $this->addCssToList($lstCss, $css);
        }

        $this->set('lstCss', $lstCss);
    }

    function addCssToList(&$list, $css)
    {
        $file = OS_PATH_PUBLIC . WEB_PATH_CSS . DS . $css.'.less.cache';

        $info = array(
            'css' => $css,
            'exists' => true,
            'timestamp' => '',
        );

        if (file_exists($file))
        {
            $cache = unserialize(file_get_contents($file));
            $timestamp = $cache['updated'];
            $date = date('m-d-Y H:i:s', $timestamp);

            $info['timestamp'] = $date;
        }
        else
        {
            //$info['css'] = $file;
            $info['exists'] = false;
        }

        $list[] = $info;
    }

    function onAction_clear_geocode_cache()
    {
        $location = new LocationModel();
        $location->clear_geocode_cache();
        $this->redirect('/admin/main');
    }

//    function onAction_update_db()
//    {
//        $export = new ExportModel();
//        $menus = $export->getMenus();
//
//        $update_ok = true;
//
//        $menumodel = new MenuModel();
//        $menumodel->beginTransaction();
//
//        foreach ($menus as $menu)
//        {
//            $menu_id = $menu['id'];
//
//            $sections = $menumodel->getSection($menu_id);
//            $mdts = $menumodel->getMetadata_old($menu_id, $sections);
//
//            $update_ok = $menumodel->updateMetadata_db($menu_id, $mdts);
//
//            if (!$update_ok)
//                break;
//        }
//
//        if ($update_ok)
//            $menumodel->commit();
//
//        //$this->redirect('/admin/main');
//    }

    function onAction_process_email_queue()
    {
        $email = new MailModel();
        $email->process_queue();
        $this->redirect('/admin/main');
    }

    function onAction_email_queue()
    {
        $this->addCss('admin/admin.email.queue');
        $mail = new MailModel();
        $queue = $mail->get_pending_emails();

        foreach ($queue as &$q)
        {
            $q['message'] = trim(strip_tags($q['message']));
        }

        $this->set('queue', $queue);
        $this->set('dbg', $queue);
    }

    function onAction_preview_email($email_id=null)
    {
        $this->m_bRender = false;

        $mail = new MailModel();
        $msg = $mail->get_email_src($email_id);

        $this->set('src', $msg);
    }
}
