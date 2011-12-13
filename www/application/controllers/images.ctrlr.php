<?php

class ImagesController
    extends Controller
{
    function onAction_pending($pending_id=null, $img_id=null)
    {
        $this->m_bRender = false;

        $img_file = $this->Images->getPendingImage($pending_id, $img_id);
        $this->set('img_file', $img_file);
    }

    function onAction_pending_sm($pending_id=null, $img_id=null)
    {
        $this->onAction_pending($pending_id, $img_id);
        $this->set('thumbnail', true);
    }

    function onAction_menu($menu_id=null, $img_id=null)
    {
        $this->m_bRender = false;

        $img_file = $this->Images->getMenuImage($menu_id, $img_id);
        $this->set('img_file', $img_file);
    }

    function onAction_menu_sm($menu_id=null, $img_id=null)
    {
        $this->onAction_menu($menu_id, $img_id);
        $this->set('thumbnail', true);
    }

    function onAction_upload($menu_id=null)
    {
        $menu = new MenuModel();
        $info = $menu->getMenuInfo($menu_id);

        if (empty($info))
        {
            Util::logit("Can't upload images to unknown menu id: {$menu_id}", __FILE__, __LINE__);
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('images/images.upload');
        $this->addJs('images/images.upload');

        $back_url = 'view';
        if (!empty($_GET) && isset($_GET['back']))
        {
            $back = $_GET['back'];

            if ($back === 'edit')
                $back_url = 'edit_metadata';
        }

        $menu_url = "/menu/{$back_url}/{$menu_id}";

        $this->set('menu_url', $menu_url);
        $this->set('id', $menu_id);
        $this->set('info', $info);

        if (empty($_FILES))
            return;

        $this->set('is_upload', true);

        $path = OS_MENU_PATH . DS . $menu_id;
        $imgs = Util::handle_upload_files($path);

        if (empty($imgs))
        {
            $this->set('is_err', true);
        }
        else
        {
            $menu->insertMenuImage($menu_id, $imgs);
        }

    }
}
