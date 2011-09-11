<?php

/** Configuration Variables **/

define('DEVELOPMENT_ENVIRONMENT',true);
define('LOG_QUERY', false);

define('DB_HOST',       '127.0.0.1');
define('DB_NAME',       'menudb');
define('DB_USERNAME',   'henry');
define('DB_PASSWORD',   'henry');

define('BASE_PATH', '/.');

define('OS_PATH_PUBLIC',        ROOT . DS . 'public');
define('OS_UPLOAD_PATH',        '/home/custom_code/www.uploads');
define('OS_DEFAULT_NO_IMAGE',   ROOT . DS . 'public' .  DS . '/img/noimage.jpg');

define('OS_PURGE_PATH',         '/home/custom_code/www.purge');

define('OS_MENU_PATH',          '/home/custom_code/www.menus');

define('WEB_PATH_CSS',      DS . 'css');
define('WEB_PATH_JS',       DS . 'js');
define('WEB_PATH_OTHER',    DS . 'other');

define('PASSCODE_TROJAN',   'locomotion');

function getSiteName()
{
    // TODO: live server should not have this...

    $names = array(
        'foodify',
    );

    $size = count($names);

    $rndIdx = rand() % $size;

    return $names[$rndIdx];
}
