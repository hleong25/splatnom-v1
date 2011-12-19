<?php

class ImagesController
    extends Controller
{
    function onAction_get($where, $max_size, $where_id=null, $img_id=null)
    {
        $this->m_bRender = false;

        $images = $this->Images;

        $preferred_size = $images->getPreferredSize($max_size);

        $img_file = ImagesModel::getDefaultNoImage();
        switch ($where)
        {
            case 'pending':
                $img_file = $images->getPendingImage($where_id, $img_id);
                break;
            case 'menu':
                $img_file = $images->getMenuImage($where_id, $img_id);
                break;
        }

        if (($preferred_size !== false) &&
            (($img_file['width'] > $preferred_size['width']) ||
             ($img_file['height'] > $preferred_size['height'])) )
        {
            $this->set('resize_img', true);

            $resize = ImageresizeUtil::resizeDimension(
                $img_file['width'], $img_file['height'],
                $preferred_size['width'], $preferred_size['height']
            );

            $img_file['resize_width'] = $resize['width'];
            $img_file['resize_height'] = $resize['height'];
        }

        $this->set('img_file', $img_file);
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

        $back = '';
        $back_url = 'view';
        if (!empty($_GET) && isset($_GET['back']))
        {
            $back = $_GET['back'];
        }
        else if (!empty($_POST) && isset($_POST['back']))
        {
            $back = $_POST['back'];
        }

        if (!empty($back))
        {
            $this->set('goback', $back);

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
            return;
        }

        $insertImgs = $menu->insertMenuImages($menu_id, $imgs);
        if ($insertImgs)
        {
            $this->set('new_imgs', $imgs);
        }

    }
}
