<?php

//if ($img_file === DEFAULT_NO_IMAGE)
//{
//    $img_file = ROOT . DS . 'public' .  DS . $img_file;
//}

$mime = mime_content_type($img_file);

header("Content-type: $mime");

readfile($img_file);