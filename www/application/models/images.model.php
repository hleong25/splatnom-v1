<?php

class ImagesModel
    extends Model
{
    function getPendingImage($img)
    {
        $img_file = OS_DEFAULT_NO_IMAGE;

        if (!empty($img))
        {
            $upload_img = OS_UPLOAD_PATH . DS . $img;
            if (file_exists($upload_img))
                $img_file = $upload_img;
        }

        return $img_file;
    }

    function getMenuImage($menu_id, $img)
    {
        $img_file = OS_DEFAULT_NO_IMAGE;

        if (!empty($menu_id) && !empty($img))
        {
            $menu_img = OS_MENU_PATH . DS . $menu_id . DS . $img;
            if (file_exists($menu_img))
                $img_file = $menu_img;
        }

        return $img_file;
    }
}
