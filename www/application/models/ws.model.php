<?php

class WsModel
    extends Model
{
    function search()
    {
        $query = 'SELECT * FROM tblPlaceContact';
        $rows = $this->query($query);
        $rows->setFetchMode(PDO::FETCH_ASSOC);
        return var_export($rows->fetchAll(), true);
    }

    function getImage($img)
    {
        $img_file = OS_DEFAULT_NO_IMAGE;

        if (!empty($img))
        {
            $upload_img = OS_UPLOAD_PATH . DS . $img;
            if (file_exists($upload_img))
                $img_file = $upload_img;

            $menu_img = OS_MENU_PATH . DS . $img;
            $menu_img = str_replace('@', DS, $menu_img);
            if (file_exists($menu_img))
                $img_file = $menu_img;
        }

        return $img_file;
    }
}
