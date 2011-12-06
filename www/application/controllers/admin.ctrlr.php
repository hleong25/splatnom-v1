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

        $this->addCss('table');
        //$this->addCss('admin/admin.pendingmenu.list');

        $this->addJs('admin/admin.pendingmenu.list');

        $this->set('new_menus', $menu->getPendingMenus());
    }

    function onAction_find_user()
    {
        $this->addCss('table');
        $this->addCss('admin/admin.find.user');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
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
        $permissions = $user->getUserPermission($id);

        $this->set('whoami', sprintf('%s: %s', $info['id'], $info['username']));
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

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

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
                // TODO: do custom search
                $search_arg = array(
                    'name' => $_POST['name'],
                    'location' => $_POST['location'],
                );

                $search_rst = array(
                );

                $this->set('search_arg', $search_arg);
                $this->set('search_rst', $search_rst);

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
        $this->addCss('table');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

        $param = false;

        if (empty($_GET))
            return;

        $param = $_GET;

        $loc = new LocationModel();

        switch ($opt)
        {
            case 'zip':
                $zip = $param['zip'];
                $this->set('q_zip', $zip);

                $latlong = $loc->getLatLongByZip($zip);

                if (!$latlong)
                    break;

                $lat = $latlong['latitude'];
                $long = $latlong['longitude'];

                $this->set('q_lat', $lat);
                $this->set('q_long', $long);

                break;
            case 'citystate':
                $citystate = $param['citystate'];
                $this->set('q_citystate', $citystate);

                $citystate = $loc->parseCityState($citystate);
                if (!$citystate)
                    break;

                $city = $citystate['city'];
                $state = $citystate['state'];

                $latlong = $loc->getLatLongByCityState($city, $state);

                if (!$latlong)
                    break;

                $lat = $latlong['latitude'];
                $long = $latlong['longitude'];

                $this->set('q_lat', $lat);
                $this->set('q_long', $long);

                break;
            case 'address':
                $address = $param['address'];
                $this->set('q_address', $address);

                $filter = explode(',', $address);
                foreach ($filter as &$key)
                {
                    $key = trim($key);
                }

                $locations = $loc->getLocationsByAddress($address, $filter);
                $this->set('found_locations', $locations);

                break;

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
        $this->addJqueryUi();
        $this->addJs('admin/admin.jqueryui.test');
        $this->addCss('admin/admin.jqueryui.test');
    }

}
