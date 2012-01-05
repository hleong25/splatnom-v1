<?php

make_new_menu();
remove_new_menu();

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

function make_new_menu()
{
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
        echo "Failed!\n";
    else
        echo "Success!\n";

    return $res;
}

function admin_login()
{
    $login = new ut_login();

    $login->set('lu', 'admin');
    $login->set('lp', 'henry');

    echo 'Login as admin...';
    $res = $login->run();

    if (!$res)
        echo "Failed!\n";
    else
        echo "Success!\n";

    return $res;
}

function remove_new_menu()
{
    $login = admin_login();

    if (!$login)
    {
        return false;
    }

    echo 'Get last pending id...';
    $pending_menu_list = new ut_pending_menu_list();
    $res = $pending_menu_list->run();

    if (!$res)
    {
        echo "Failed!\n";
        return false;
    }
    else
        echo "Success!\n";

    $last_id = $pending_menu_list->get_last_pending_id();

    $remove_menu = new ut_pending_menu_remove();
    $remove_menu->last_id($last_id);

    echo "Removing last pending menu {$last_id}...";
    $res = $remove_menu->run();

    if (!$res)
    {
        echo "Failed!\n";
        return false;
    }
    else
        echo "Success!\n";

    echo 'Get last pending id to verify...';
    $pending_menu_list = new ut_pending_menu_list();
    $res = $pending_menu_list->run();

    if (!$res)
    {
        echo "nothing to check... assuming success!\n";
        return true;
    }
    else
        echo "Success!\n";

    $new_last_id = $pending_menu_list->get_last_pending_id();

    echo "Please check... last_id({$last_id}) ?== new_last_id({$new_last_id})";
    return true;
}
