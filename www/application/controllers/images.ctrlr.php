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

        $this->set('img_file', $img_file);

        if ($img_file['filename'] === OS_DEFAULT_NO_IMAGE_FILE)
            return; // don't resize default no image

        if ($preferred_size === false)
            return; // preferred size is false...

        if (($img_file['width'] < $preferred_size['width']) ||
            ($img_file['height'] < $preferred_size['height']))
            return; // size is within range of preferred size

        // if it's here, then we want to use the thumbnails...
        // just gotta see if we want to create it or re-use it

        $new_path = $img_file['path'] .DS . $max_size;
        $save_file = $new_path . DS . $img_file['filename'];

        $new_size = array('width'=>$img_file['width'], 'height'=>$img_file['height']);
        if (file_exists($save_file))
        {
            // don't need to re-create the thumbnail
            $new_size = ImageresizeUtil::resizeDimension
            (
                $img_file['width'], $img_file['height'],
                $preferred_size['width'], $preferred_size['height']
            );
        }
        else
        {
            // okay... we need to create the thumbnail
            $img_src = $img_file['path'] . DS . $img_file['filename'];
            $resize = new ImageresizeUtil($img_src);
            $new_size = $resize->resizeImage($preferred_size['width'], $preferred_size['height']);

            if (!file_exists($new_path))
            {
                $path_ok = mkdir($new_path, 0755, true);
                if (!$path_ok)
                {
                    Util::logit("Failed to create path '{$new_path}'", __FILE__, __LINE__);
                    return;
                }
            }

            $bSaved = $resize->saveImage($save_file);

            if ($bSaved !== true)
            {
                Util::logit("Failed to create thumbnail for '{$img_src}'", __FILE__, __LINE__);
                return;
            }

            //Util::logit("Successfully created thumbnail '{$save_file}' ", __FILE__, __LINE__);
        }

        $img_file['path'] = $new_path;
        $img_file['width'] = $new_size['width'];
        $img_file['height'] = $new_size['height'];

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
