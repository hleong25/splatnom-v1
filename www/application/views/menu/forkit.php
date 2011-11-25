<?php
$params = array(
    'data' => array(),
);

extract($params, EXTR_SKIP);


$json = json_encode($data);
$size = strlen($json);

header('Content-type: application/json');
header('Content-Length: ' . $size);

echo $json;
