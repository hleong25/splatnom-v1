<?php

class ImagesModel
    extends Model
{
    static function getDefaultNoImage()
    {
        return array
        (
            'filename' => OS_DEFAULT_NO_IMAGE,
            'width' => OS_DEFAULT_NO_IMAGE_WIDTH,
            'height' => OS_DEFAULT_NO_IMAGE_HEIGHT,
        );
    }

    static function getPreferredSize($max_size)
    {
        switch ($max_size)
        {
            case 'sm':
                return array('width'=>100, 'height'=>100);
                break;
            case 'md':
                return array('width'=>500, 'height'=>500);
                break;
        }

        $parsed = sscanf($max_size, '%dx%d', $width, $height);
        if ($parsed === 2)
            return array('width'=>(int)$width, 'height'=>(int)$height);

        return false;
    }

    function getImageDimensions($where, $id, $img)
    {
        $dimens = array
        (
            'width' => OS_DEFAULT_NO_IMAGE_WIDTH,
            'height' => OS_DEFAULT_NO_IMAGE_HEIGHT,
        );

        if ($img === OS_DEFAULT_NO_IMAGE)
            return $dimens;

        $dimens = array
        (
            'width' => 0,
            'height' => 0,
        );

        $query = false;
        switch ($where)
        {
            case 'pending':
                $query =<<<EOQ
                    SELECT width, height
                    FROM tblPendingMenuImages
                    WHERE pendingmenu_id = :id
                    AND file_img = :img
EOQ;
                break;
            case 'menu':
                $query =<<<EOQ
                    SELECT width, height
                    FROM tblMenuImages
                    WHERE menu_id = :id
                    AND file_img = :img
EOQ;
                break;
        }

        if ($query === false)
            return $dimens;

        $params = array(':id'=>$id, ':img'=>$img);
        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return $dimens;

        $img_dimens = $prepare->fetch(PDO::FETCH_ASSOC);
        if (empty($img_dimens)) return $dimens;

        return $img_dimens;
    }

    function getPendingImage($pending_id, $img)
    {
        $img_file = ImagesModel::getDefaultNoImage();

        if (!empty($pending_id) && !empty($img))
        {
            $upload_img = OS_UPLOAD_PATH . DS . $pending_id . DS . $img;
            if (file_exists($upload_img))
            {
                $img_file = $this->getImageDimensions('pending', $pending_id, $img);
                $img_file['filename'] = $upload_img;
            }
        }

        return $img_file;
    }

    function getMenuImage($menu_id, $img)
    {
        $img_file = ImagesModel::getDefaultNoImage();

        if (!empty($menu_id) && !empty($img))
        {
            $menu_img = OS_MENU_PATH . DS . $menu_id . DS . $img;
            if (file_exists($menu_img))
            {
                $img_file = $this->getImageDimensions('menu', $menu_id, $img);
                $img_file['filename'] = $menu_img;
            }
        }

        return $img_file;
    }
}
