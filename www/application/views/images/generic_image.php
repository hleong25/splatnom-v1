<?php
// NOTE: image caching
//       http://drupal.org/node/25977
//       http://betterexplained.com/articles/how-to-optimize-your-site-with-http-caching/
//       http://rd2inc.com/blog/2005/03/making-dynamic-php-pages-cacheable/

$img_src = $img_file['path'] . DS . $img_file['filename'];

if (!file_exists($img_src))
{
    $img_src = OS_DEFAULT_NO_IMAGE_PATH . DS . OS_DEFAULT_NO_IMAGE_FILE;
}

$request = apache_request_headers();

//$referer = $request['Referer'];

$mime = mime_content_type($img_src);

$stat = stat($img_src);
$inode = $stat['ino'];
$mtime = $stat['mtime'];
$fsize = $stat['size'];

$etag = sprintf('"%x-%x-%x"', $inode, $fsize, $mtime);

if (isset($request['If-Modified-Since']) && isset($request['If-None-Match']))
{
    //remove information after the semicolon and form a timestamp
    $request_mtime = explode(';', $request['If-Modified-Since']);
    $request_mtime = strtotime($request_mtime[0]);

    $request_etag = $request['If-None-Match'];

    if (($mtime <= $request_mtime) && ($etag === $request_etag))
    {
        //Util::logit("Cached on client side: '{$referer}'");
        header('HTTP/1.1 304 Not Modified');
        header("Content-Type: $mime");
        header('Cache-Control: public');
        header("ETag: {$etag}");
        exit();
    }
    else
    {
        // shouldn't be here... but if it does, lets analyze the output
        Util::logit("Cache miss: mtime('{$mtime}', '$request_mtime') etag('{$etag}', '{$request_etag}')");
    }
}

//Util::logit("Not cached on client side: '{$referer}'");

$modified_date = gmstrftime("%a, %d %b %Y %T %Z", $mtime);

header("Content-Type: $mime");
header("Content-Length: $fsize");
header('Cache-Control: public');
header('Expires: ');
header("Last-Modified: {$modified_date}");
header("ETag: {$etag}");

readfile($img_src);
