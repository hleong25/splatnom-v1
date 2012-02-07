<?php

/** Configuration Variables **/

define('SITE_NAME', 'splatnom');

define('DEVELOPMENT_ENVIRONMENT',true);
define('LOG_QUERY', false);

if (file_exists('db.inc.php'))
{
    require_once('db.inc.php');
}
else
{
    define('DB_HOST',       '127.0.0.1');
    define('DB_NAME',       'menudb');
    define('DB_USERNAME',   'henry');
    define('DB_PASSWORD',   'henry');
}

define('SQL_AES_KEY',   'tr0nL3g@c4');

if (file_exists('paths.inc.php'))
{
    require_once('paths.inc.php');
}
else
{
    define('OS_PATH_PUBLIC',        ROOT . DS . 'public');
    define('OS_UPLOAD_PATH',        '/home/custom_code/www.uploads');
    define('OS_IMAGE_PATH',         ROOT . DS . 'public' .  DS . 'img');
    define('OS_PURGE_PATH',         '/home/custom_code/www.purge');
    define('OS_MENU_PATH',          '/home/custom_code/www.menus');
    define('OS_TEMP_PATH',          '/home/custom_code/www.temp');

    define('OS_DEFAULT_NO_IMAGE_PATH',      OS_IMAGE_PATH);
    define('OS_DEFAULT_NO_IMAGE_FILE',      'noimage.jpg');
    define('OS_DEFAULT_NO_IMAGE_WIDTH',     258);
    define('OS_DEFAULT_NO_IMAGE_HEIGHT',    196);
}

define('WEB_PATH_CSS',      DS . 'css');
define('WEB_PATH_JS',       DS . 'js');
define('WEB_PATH_OTHER',    DS . 'other');

define('PASSCODE_TROJAN',   'locomotion');
