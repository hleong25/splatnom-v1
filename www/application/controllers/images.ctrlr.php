<?php

class ImagesController
    extends Controller
{
    function onAction_get($where, $max_size, $where_id=null, $img_filename=null)
    {
        $this->m_bRender = false;

        $images = $this->Images;

        $preferred_size = $images->getPreferredSize($max_size);

        $img_file = ImagesModel::getDefaultNoImage();
        switch ($where)
        {
            case 'pending':
                $img_file = $images->getPendingImage($where_id, $img_filename);
                break;
            case 'menu':
                $img_file = $images->getMenuImage($where_id, $img_filename);
                break;
            case 'event':
                $img_file = $images->getEventImage($where_id, $img_filename);
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

    function onAction_upload($type)
    {
        $type = trim(strtolower($type));

        $func_name = '';

        $upload_args = func_get_args();
        array_shift($upload_args);

        switch ($type)
        {
            case 'menu':
                $func_name = 'upload_menu_images';
                break;

            case 'event':
                $func_name = 'upload_event_images';
                break;

            default:
                Util::logit("Unknown upload type '$type'", __FILE__, __LINE__);
                $this->redirect('/home/main');
                return;
        }

        call_user_func_array(array($this, $func_name), $upload_args);
    }

    function upload_menu_images($menu_id=null, $section_id=null, $item_id=null)
    {
        $this->override_body_page_name('upload.menu');

        $menu = new MenuModel();
        $info = $menu->getMenuInfo($menu_id);

        if (empty($info))
        {
            Util::logit("Can't upload images to unknown menu id: {$menu_id}", __FILE__, __LINE__);
            $this->redirect('/home/main');
            return;
        }

        $user_id = Util::getUserId();
        if (empty($user_id))
        {
            $this->redirect("/menu/view/{$menu_id}");
            return;
        }

        $this->addCss('images/images.upload');
        $this->addJqueryUi();
        $this->addJs('images/images.upload');

        $this->set('id', $menu_id);
        $this->set('info', $info);

        $id_names = $menu->getIdAndNames($menu_id, $section_id, $item_id);

        $this->set('menu_id', $id_names['menu_id']);
        $this->set('section_id', $id_names['section_id']);
        $this->set('item_id', $id_names['item_id']);

        $this->set('menu_str', $id_names['menu']);
        $this->set('section_str', $id_names['section']);
        $this->set('item_str', $id_names['item']);

        if (!empty($_FILES))
        {
            $this->set('is_upload', true);

            $tags = $menu->getMenuTags($menu_id);
            $this->set('tags', $tags);

            $path = OS_MENU_PATH . DS . $menu_id;
            $imgs = Util::handle_upload_files($path);

            if (empty($imgs))
            {
                $this->set('is_err', true);
                return;
            }

            $insertImgs = $menu->insertMenuImages($menu_id, $user_id, $imgs);

            if ($insertImgs)
            {
                $this->set('new_imgs', $imgs);

                if (!empty($id_names['section_id']) && !empty($id_names['item_id']))
                {
                    $taggits[] = array('sid'=>$id_names['section_id'], 'mid'=>$id_names['item_id']);

                    foreach ($imgs as $img)
                    {
                        $menu->updateTaggitsImage($menu_id, $img['filename'], $taggits, null);
                    }
                }
            }
        }
    }
}
