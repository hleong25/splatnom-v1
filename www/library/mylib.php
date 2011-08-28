<?php

function logit($obj)
{
    error_log(var_export($obj, true));
}

function redirect($location)
{
    header("Location: {$location}");
}


function handle_upload_files($bFakeTransfer = false) 
{
    if (empty($_FILES))
        return false;

//    $db = get_db_conn();
    $files = array();

    foreach ($_FILES as $key => $file) 
    {
        if (is_array($file['name']))
        {
            foreach ($file['name'] as $idx => $val)
            {
                if ($file['tmp_name'][$idx] === '')
                    continue;

                $ret = handle_upload_files_helper($bFakeTransfer, $file['tmp_name'][$idx], $file['name'][$idx]);
                
                if ($ret !== false)
                {
                    $files[] = $ret;
                }
                else
                {
                    error_log('Failed to handle uploaded file');
                }
            }
        }
        else
        {
            if ($file['tmp_name'] === '')
                continue;
            
            $ret = handle_upload_files_helper($bFakeTransfer, $file['tmp_name'], $file['name']);
            
            if ($ret !== false)
            {
                $files[] = $ret;
            }
            else
            {
                error_log('Failed to handle uploaded file');
            }
        }
    }

    return $files;
}

function handle_upload_files_helper($bFakeTransfer, $tmp_name, $name)
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
        $unique_id = date('ymdHis') . uniqid();
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

    if ($bFakeTransfer === false)
        rename($tmp_name, $uploaded_file);

    return $new_filename;
}