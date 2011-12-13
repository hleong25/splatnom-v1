<?php

class Util
{
    function logit($obj, $file=null, $line=null)
    {
        $from = "";
        if (!empty($file) && !empty($line))
            $from = "({$file}:{$line}): ";

        if (is_string($obj))
            error_log($from.$obj);
        else
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

    static function handle_upload_files($path)
    {
        $uploader = new UploadHandler($path);
        return $uploader->handle_upload_files();
    }
}

class UploadHandler
{
    private $m_path;

    function __construct($path)
    {
        $this->m_path = $path;

        if (!file_exists($path))
        {
            if (!mkdir($path, 0755, true))
            {
                Util::logit("Path '{$path}' does not exists. Failed to create it.", __FILE__, __LINE__);
                $this->m_path = null;
            }
        }
    }

    function handle_upload_files()
    {
        if (empty($this->m_path) || !file_exists($this->m_path))
        {
            Util::logit("handle_upload_files() failed, path '{$this->m_path}' not found", __FILE__, __LINE__);
            return false;
        }

        if (empty($_FILES))
            return false;

        $files = array();

        // TODO:: handle $_FILES[error] for error codes
        //        also look into removing the php_value in .htaccess or php.ini and see why the
        //        error isn't being set when max is met
        //        for example, change to default 8M upload max, and then upload 10M, the $_FILES
        //        empty and theres no info on that at all.

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
        $unique_id = '';
        for ($ii = 0, $jj = 100; $ii < $jj; $ii++)
        {
            // this will retry up to 5 times to get a unique filename
            //$unique_id = date('ymdHisu') . uniqid(); // TODO: 'u' is supported on 5.2.2+
            $unique_id = date('ymdHis') . uniqid();
            $short_id = base_convert($unique_id, 10, 36); // 0-9,a-z
            $new_filename = "{$short_id}.{$file_ext}";
            $uploaded_file = $this->m_path . DS . $new_filename;

            if (!file_exists($uploaded_file))
                break;

            $uploaded_file = false;
        }

        if ($uploaded_file === false)
        {
            // failed to get a unique filename...
            Util::logit('Failed to create unique filename... even after 100 tries', __FILE__, __LINE__);
            return false;
        }

        //$move_ok = @rename($tmp_name, $uploaded_file);
        $move_ok = @move_uploaded_file($tmp_name, $uploaded_file);
        $move_file = false;
        if ($move_ok)
        {
            //Util::logit("handle_upload_files_helper(): {$unique_id} -> {$uploaded_file}");

            //@chmod($uploaded_file, 0644);

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
