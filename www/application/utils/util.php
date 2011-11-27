<?php

class Util
{
    function logit($obj, $file=null, $line=null)
    {
        $from = "";
        if (!empty($file) && !empty($line))
            $from = "({$file}:{$line}): ";
        error_log($from.var_export($obj, true));
    }

    static function redirect($location)
    {
        header("Location: {$location}");
    }

    static function normalizeUrl($url)
    {
        $url = trim($url);

        if (empty($url))
            return '';

        $scheme = parse_url($url, PHP_URL_SCHEME);

        if (empty($scheme))
            $url = 'http://'.$url;

        return $url;
    }

    static function getUserId()
    {
        if (!isset($_SESSION['id']))
            return false;

        return $_SESSION['id'];
    }

    static function getPermissions($key=null)
    {
        $perms = array();

        if (isset($_SESSION['perms']) && !empty($_SESSION['perms']))
            $perms = $_SESSION['perms'];

        if ($key === null)
            return $perms;

        if (array_key_exists($key, $perms))
            return $perms[$key];
        else
            return false;
    }

    static function clearPermissions()
    {
        unset($_SESSION['perms']);
    }

    static function handle_upload_files()
    {
        $uploader = new UploadHandler();
        return $uploader->handle_upload_files();
    }
}

class UploadHandler
{
    function handle_upload_files()
    {
        if (empty($_FILES))
            return false;

        $files = array();

        foreach ($_FILES as $key => $file)
        {
            if (is_array($file['name']))
            {
                foreach ($file['name'] as $idx => $val)
                {
                    if ($file['tmp_name'][$idx] === '')
                        continue;

                    $ret = $this->handle_upload_files_helper($file['tmp_name'][$idx], $file['name'][$idx]);

                    if ($ret !== false)
                    {
                        $files[] = $ret;
                    }
                    else
                    {
                        Util::logit('Failed to handle uploaded file', __FILE__, __LINE__);
                    }
                }
            }
            else
            {
                if ($file['tmp_name'] === '')
                    continue;

                $ret = $this->handle_upload_files_helper($file['tmp_name'], $file['name']);

                if ($ret !== false)
                {
                    $files[] = $ret;
                }
                else
                {
                    Util::logit('Failed to handle uploaded file', __FILE__, __LINE__);
                }
            }
        }

        return $files;
    }

    function handle_upload_files_helper($tmp_name, $name)
    {
        // TODO: might need to change this to finfo_file
        $mime = mime_content_type($tmp_name);

        if (strpos($mime, 'image') !== 0)
            return false;

        $file_ext = pathinfo($name, PATHINFO_EXTENSION);
        //$file_name = 'menu_' . date('ymdHis');

        $uploaded_file = false;
        for ($ii = 0, $jj = 5; $ii < $jj; $ii++)
        {
            // this will retry up to 5 times to get a unique filename
            $unique_id = date('ymdHisu') . uniqid();
            $short_id = base_convert($unique_id, 10, 36); // 0-9,a-z
            $new_filename = "menu_{$short_id}.{$file_ext}";
            $uploaded_file = OS_UPLOAD_PATH . DS . $new_filename;

            if (!file_exists($uploaded_file))
                break;

            $uploaded_file = false;
        }

        if ($uploaded_file === false)
            // failed to get a unique filename...
            return false;

        $move_ok = @rename($tmp_name, $uploaded_file);
        $move_file = false;
        if ($move_ok)
        {
            $img = new ImageresizeUtil($uploaded_file);

            $move_file = array
            (
                'filename' => $new_filename,
                'width' => $img->getWidth(),
                'height' => $img->getHeight(),
            );
        }

        return $move_file;
    }


}
