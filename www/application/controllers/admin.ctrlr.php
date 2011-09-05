<?php

class AdminController
    extends Controller
{
    function onAction_main()
    {
        $this->addCss('admin/admin.main');
        $this->addJs('admin/admin.main');
        
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
        $this->addJs('admin/admin.find.user');
        
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        
        // default values
        $this->set('query_user', '');
        $this->set('query_result', null);
        
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
            $this->m_bRender = false;
            redirect('/admin/find_user');
            return;
        }
        
        $user = new UserModel();
        
        if (!empty($_POST))
        {
            $settings = array();
            $settings['permissions'] = array();
            
            if (!empty($_POST['perms']))
                $settings['permissions'] = $_POST['perms'];
            
            $this->handle_profile_update($user, $id, $settings);
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
    
    //function onAction_staging($id = null)
    //{
        //if (empty($id) || ($id < 0))
        //{
            //$this->m_bRender = false;
            //redirect('/admin/pendingmenu_list');
            //return;
        //}
        //$this->addCss('admin/admin.staging');
        //$this->addJs('admin/admin.staging');
        
        //$this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        
        //$menu = new MenuModel();
        //$menu_data = $menu->getNewMenu($id);
        
        //if ($menu_data === false)
        //{
            //$this->m_bRender = false;
            //redirect('/admin/pendingmenu_list');
            //return;
        //}
        
        //$this->set('staging_id', $id);
        //$this->set('site', $menu_data['site']);
        //$this->set('imgs', $menu_data['imgs']);
        //$this->set('info', array());
        
        //$menus = isset($_POST['menu']) ? $_POST['menu'] : array();
        //$menus = $this->parsePostStagingMenu($menus);
        //$menus = $this->validateStagingMenu($menus);
        //$this->set('menus', $menus);
        
        //$this->set('dbg', 
            //array(
                //'post' => isset($_POST) ? $_POST : array(),
                //'menus' => $menus,
            //)
        //);
    //}
    
    //function parsePostStagingMenu($postMenu)
    //{
        //$menuCollection = array();
        //$menu = array();
        
        //for ($ii = 0, $jj = count($postMenu); $ii < $jj; $ii++)
        //{
            //switch ($postMenu[$ii])
            //{
                //case '@menu@':
                    //$menu = array(
                        //'name' => $postMenu[++$ii],
                        //'notes' => $postMenu[++$ii],
                    //);
                    //break;
                //case '@item@':
                    //$menu['items'][] = array(
                        //'item' => $postMenu[++$ii],
                        //'price' => $postMenu[++$ii],
                        //'notes' => $postMenu[++$ii],
                    //);
                    //break;
                //case '@end_of_menu@':
                    //$menuCollection[] = $menu;
                    //break;
            //}
        //}
        
        //return $menuCollection;
    //}
    
    //function validateStagingMenu($menus)
    //{
        //if (empty($menus))
            //$menus[] = array();
        
        //foreach ($menus as $idx_menu => &$menu)
        //{
            //if (!isset($menu['name']))
                //$menu['name'] = '';

            //if (!isset($menu['notes']))
                //$menu['notes'] = '';

            //if (empty($menu['items']))
                //$menu['items'][] = array();

            //foreach ($menu['items'] as $idx_item => &$item)
            //{
                //if (empty($item))
                    //$item = array(
                        //'item' => '',
                        //'price' => '',
                        //'notes' => '',
                    //);

                //if (!isset($item['item']))
                    //$item['item'] = '';

                //if (!isset($item['price']))
                    //$item['price'] = '';

                //if (!isset($item['notes']))
                    //$item['notes'] = '';
            //}
        //}
        
        //return $menus;
    //}

    function onAction_pending_menu($id = null)
    {
        if (empty($id) || ($id < 0))
        {
            $this->m_bRender = false;
            redirect('/admin/pendingmenu_list');
            return;
        }
        $this->addCss('admin/admin.pending.menu');
        $this->addJs('admin/admin.pending.menu');
        
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
 
        $menu = new MenuModel();
        $menu_data = $menu->getPendingMenu($id);
        
        if ($menu_data === false)
        {
            $this->m_bRender = false;
            redirect('/admin/pendingmenu_list');
            return;
        }
        
        $this->set('pending_id', $id);
        $this->set('err_msg', empty($_GET['err_msg']) ? '' : $_GET['err_msg']);
        $this->set('site', $menu_data['site']);
        $this->set('imgs', $menu_data['imgs']);
        $this->set('search_arg', array('name'=>'', 'location'=>''));
        //$this->set('search_rst', array());

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
            redirect('/admin/pendingmenu_list');
            return;
        }

        $menu = new MenuModel();
        $new_menu = $menu->pendingMenuApproved($id);
        if ($new_menu === false)
        {
            $msg = '&err_msg=failed';
            redirect('/admin/pending_menu/'.$id.'/'.$msg);
            return;
        }
        else
        {
            redirect('/menu/metadata/'.$new_menu);
            return;
        }
    }
}
