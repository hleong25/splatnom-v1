<?php
// NOTE: image caching
//       http://drupal.org/node/25977
//       http://betterexplained.com/articles/how-to-optimize-your-site-with-http-caching/

/*
    From testing, 'If-Modified-Since' doesn't works 100%.
    Maybe look into ETags
*/

$img_src = $img_file['path'] . DS . $img_file['filename'];

if (!file_exists($img_src))
{
    $img_src = OS_DEFAULT_NO_IMAGE_PATH . DS . OS_DEFAULT_NO_IMAGE_FILE;
}

$request = apache_request_headers();
$modified = filemtime($img_src);

if (isset($request['If-Modified-Since']))
{
    //remove information after the semicolon and form a timestamp
    $request_modified = explode(';', $request['If-Modified-Since']);
    $request_modified = strtotime($request_modified[0]);

    if ($modified <= $request_modified)
    {
        header('HTTP/1.1 304 Not Modified');
        exit();
    }
}

$mime = mime_content_type($img_src);

$modified_date = gmdate('D, d M Y H:i:s', $modified);

header("Content-type: $mime");
header("Last-Modified: {$modified_date} GMT");
header('Cache-Control: public');

readfile($img_src);
