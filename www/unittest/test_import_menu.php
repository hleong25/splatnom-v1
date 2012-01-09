<?php

$menu_id = import_menu();

if (empty($menu_id))
    return;

validate_menu($menu_id);
purge_menu($menu_id);

logit('Done.');

function admin_login()
{
    $login = new ut_login();

    $login->set('lu', 'admin');
    $login->set('lp', 'henry');

    echo 'Login as admin...'."\n";
    $res = $login->run();

    if (!$res)
        echo "Failed!\n";

    return $res;
}

function import_menu()
{
    $login = admin_login();

    if (!$login)
        return false;

    $file = OS_TEMP_PATH.'/menu.unittest.1.txt';

    if (!is_readable($file))
    {
        echo "File '{$file}' does not exist or not readable.";
        return false;
    }

    $file = "@{$file}";

    $import = new ut_menu_import();
    $import->set('Import!', 'Submit');
    $import->set('import_file', $file);

    echo 'Import menu...';
    $res = $import->run();

    if (!$res)
    {
        echo 'Failed.';
        return false;
    }

    return $import->getNewMenuId();
}

function validate_menu($menu_id)
{
    $link = "http://www.gogomenu.com/menu/edit_metadata/{$menu_id}";

    $go = new ut_golink();
    $go->link($link);

    echo "Validate menu by going to '{$link}'...\n";
    $res = $go->run();

    $html_page = $go->curl_exec();

    $doc = new DOMDocument();
    @$doc->loadHTML($html_page);

    $xpath = new DOMXpath($doc);
    $elems_menu = $xpath->query("//div[@class='pg pg_bottom menu']");

    foreach ($elems_menu as $menu)
    {
        validate_menu_group($menu);
    }
}

function validate_menu_group($menu)
{
    $owner_doc = $menu->ownerDocument;

    //logit('-----------------------');
    $xml = $owner_doc->saveXML($menu);
    //logit($xml);

    $doc = DOMDocument::loadHTML($xml);
    $xpath = new DOMXpath($doc);

    $elem_group = $xpath->query("//div[@class='heading onToggle']");
    if ($elem_group->length != 1)
    {
        echo "Failed to get heading...\n";
        return false;
    }

    $group = trim($elem_group->item(0)->nodeValue);
    logit("Checking '{$group}'");

    $elem_notes = $xpath->query("//input[@title='Group notes']");
    if ($elem_notes->length != 1)
    {
        echo "Failed to get notes...\n";
        return false;
    }

    $notes = $elem_notes->item(0)->attributes->getNamedItem('value')->nodeValue;
    $item_check = (int)$notes;
    logit("\tChecking {$item_check} items...");

    $elem_items = $xpath->query("//input[@value='@item@']");
    logit("\tThis group contains {$elem_items->length} items...");

    $is_ok = $item_check == $elem_items->length;

    if (!$is_ok)
    {
        logit("\tMenu section failed.");
        return false;
    }
    else
    {
        logit("\tMenu section success.");
    }

    return true;
}

function purge_menu($menu_id)
{
    $link = "http://www.gogomenu.com/menu/purge/{$menu_id}";

    $go = new ut_golink();
    $go->link($link);

    logit("Purge menu by going to '{$link}'...");
    $res = $go->run();

    $info = $go->curl_getinfo();

    $http_code = $info['http_code'];

    if ($http_code != 302)
    {
        logit('Failed.');
        return false;
    }

    return true;
}
