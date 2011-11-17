<?php
$params = array(
    'export_data' => array(),
    'file' => 'menu.txt',
    'out' => null,
);

extract($params, EXTR_SKIP);

if ($out === 'json')
{
    $data = json_encode($export_data);
    $size = strlen($data);

    header('Content-type: application/json');
    header('Content-Disposition: attachment; filename="'.$file.'"');
    header('Content-Length: ' . $size);

    echo $data;
}
else
{
    header('Content-type: text/plain');

    var_export($export_data);
}
