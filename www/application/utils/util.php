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

    static function getUniqueString()
    {
        $now_date = date('ymdHis'); // TODO: ymdHisu -> 'u' is supported on 5.2.2+
        $unique_id = base_convert(date('ymdHis'), 10, 36); // 0-9,a-z
        $short_id = $unique_id . uniqid();

        return $short_id;
    }

    static function decodeUniqueString($src)
    {
        $base36_cnt = 8;
        $base36 = substr($src, 0, $base36_cnt);

        if (empty($base36))
            return false;

        $date = base_convert($base36, 36, 10);
        $uniqid = substr($src, $base36_cnt);

        if (empty($uniqid))
            return false;

        $decode = array
        (
            'date' => $date,
            'unique_id' => $uniqid,
        );

        return $decode;
    }

    static function handle_upload_files($path)
    {
        $uploader = new UploadHandler($path);
        return $uploader->handle_upload_files();
    }

    // Modifies a string to remove all non ASCII characters and spaces.
    // http://sourcecookbook.com/en/recipes/8/function-to-slugify-strings-in-php
    static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv'))
        {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }

    static function getUnseo($text)
    {
        $parse = explode('-', $text);
        return $parse;
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

        $handle_files = array();
        foreach ($_FILES as $key => $file)
        {
            if (is_array($file['name']))
            {
                foreach ($file['name'] as $idx => $val)
                {
                    if ($file['tmp_name'][$idx] === '')
                        continue;

                    $tmp_name = $file['tmp_name'][$idx];
                    $name = $file['name'][$idx];
                    $mime = mime_content_type($tmp_name); // TODO: might need to change this to finfo_file
                    $file_ext = pathinfo($name, PATHINFO_EXTENSION);

                    $handle_files[] = array
                    (
                        'tmp_name'=>$tmp_name,
                        'name'=>$name,
                        'mime'=>$mime,
                        'file_ext'=>$file_ext,
                    );
                }
            }
            else
            {
                if ($file['tmp_name'] === '')
                    continue;

                $tmp_name = $file['tmp_name'];
                $name = $file['name'];
                $mime = mime_content_type($tmp_name); // TODO: might need to change this to finfo_file
                $file_ext = pathinfo($name, PATHINFO_EXTENSION);

                $handle_files[] = array
                (
                    'tmp_name'=>$tmp_name,
                    'name'=>$name,
                    'mime'=>$mime,
                    'file_ext'=>$file_ext,
                );
            }
        }

        foreach ($handle_files as $hfile)
        {
            $info = $this->upload_helper_image($hfile, true);
            if ($info !== false)
            {
                $files[] = $info;
                continue;
            }

            $info = $this->upload_helper_zip($hfile);
            if ($info !== false)
            {
                $files = array_merge($files, $info);
                continue;
            }

            // if it's here, then it's no good...
            Util::logit("Unhandled file type. File '{$hfile['name']}', mime-type '{$hfile['mime']}'", __FILE__, __LINE__);
            @unlink($hfile['tmp_name']);
        }

        return $files;
    }

    function upload_helper_image($upload_file, $bIsUploadFiled)
    {
        $tmp_name = $upload_file['tmp_name'];
        //$name = $upload_file['name'];
        $mime = $upload_file['mime'];
        $file_ext = $upload_file['file_ext'];

        if (strpos($mime, 'image') !== 0)
        {
            // not an image type
            return false;
        }

        $rand_file = false;
        $new_filename = false;
        for ($ii = 0, $jj = 100; $ii < $jj; $ii++)
        {
            $rand_file = Util::getUniqueString() . '.' . $file_ext;
            $new_filename = $this->m_path . DS . $rand_file;

            if (!file_exists($new_filename))
                break;

            $new_filename = false;
        }

        if ($new_filename === false)
        {
            // failed to get a unique filename...
            Util::logit('Failed to create unique filename.', __FILE__, __LINE__);
            return false;
        }

        $move_ok = false;
        if ($bIsUploadFiled)
            $move_ok = @move_uploaded_file($tmp_name, $new_filename);
        else
            $move_ok = @rename($tmp_name, $new_filename);

        $move_file = false;
        if ($move_ok)
        {
            $img = new ImageresizeUtil($new_filename);

            $move_file = array
            (
                'filename' => $rand_file,
                'width' => $img->getWidth(),
                'height' => $img->getHeight(),
            );
        }

        return $move_file;
    }

    function upload_helper_zip($upload_file)
    {
        $tmp_name = $upload_file['tmp_name'];
        //$name = $upload_file['name'];
        $mime = $upload_file['mime'];
        $file_ext = $upload_file['file_ext'];

        if ($mime !== 'application/x-zip')
        {
            // not a zip type
            return false;
        }

        $zip = new ZipArchive;
        $res = $zip->open($tmp_name);

        if ($res !== true)
        {
            Util::logit("Failed to open zip file. {$name}", __FILE__, __LINE__);
            return false;
        }

        $extract_files = array();
        for ($ii = 0, $jj = $zip->numFiles; $ii < $jj; $ii++)
        {
            $zip_name = $zip->getNameIndex($ii);
            $zip_ext = pathinfo($zip_name, PATHINFO_EXTENSION);
            $zip_ext = strtolower($zip_ext);

            switch ($zip_ext)
            {
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'png':
                    $extract_files[] = $zip_name;
            }
        }

        if (empty($extract_files))
        {
            $zip->close();
            return false;
        }

        // create temp directory and extract to path
        $temp_path = OS_TEMP_PATH . DS . Util::getUniqueString();
        @mkdir($temp_path);

        $unzipped = $zip->extractTo($temp_path, $extract_files);
        if ($unzipped !== true)
        {
            Util::logit("Failed to unzip files. {$name}", __FILE__, __LINE__);
            return false;
        }

        // close and delete the zip file
        $zip->close();
        @unlink($tmp_name);

        $files = array();

        // transfer the zip files to the destination path
        foreach ($extract_files as $unzipped_file)
        {
            $tmp_name = $temp_path . DS . $unzipped_file;
            $name = $unzipped_file;
            $mime = mime_content_type($tmp_name); // TODO: might need to change this to finfo_file
            $file_ext = pathinfo($name, PATHINFO_EXTENSION);

            $uzfile = array
            (
                'tmp_name'=>$tmp_name,
                'name'=>$name,
                'mime'=>$mime,
                'file_ext'=>$file_ext,
            );

            $files[] = $this->upload_helper_image($uzfile, false);
        }

        @rmdir($temp_path);

        return $files;
    }
}
