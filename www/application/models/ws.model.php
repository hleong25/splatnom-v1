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
        }
        
        return $img_file;
    }
}