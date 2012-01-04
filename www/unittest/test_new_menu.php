<?php

function validate_files(&$files)
{
    $bIsGood = true;
    foreach ($files as &$file)
    {
        if (empty($file))
            continue;

        if (!is_readable($file))
        {
            $bIsGood = false;
            echo "Local file does not exists or cannot be read for upload: '{$file}'\n";
        }
        else
        {
            $file = '@'.$file;
        }
    }

    return $bIsGood;
}

$new_menu = new ut_menu_new();

$urls = array
(
    'www.google.com',
    'www.unit-test.com',
    '', // empty
    '', // empty
    '', // empty
);

$imgs = array
(
    OS_TEMP_PATH.'/menu1.jpg',
    OS_TEMP_PATH.'/menu2.jpg',
    OS_TEMP_PATH.'/menu3.jpg',
    OS_TEMP_PATH.'/menu4.jpg',
    '', // empty
);

// validate we have files locally
if (!validate_files($imgs))
{
    return;
}

echo 'Test good new menu...';
$new_menu->set('url', $urls);
$new_menu->set('imgs', $imgs);

$res = $new_menu->run();

if (!$res)
{
    echo 'Failed';
    return;
}
else
{
    echo "Success!\n";
}

echo "\n";
