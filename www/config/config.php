<?php

/** Configuration Variables **/

define('SITE_NAME', 'splatnom');

if (file_exists(ROOT.'/config/config.dev'))
    define('DEVELOPMENT_ENVIRONMENT',true);
else
    define('DEVELOPMENT_ENVIRONMENT',false);

if (file_exists(ROOT.'/config/db.inc.php'))
{
    require_once(ROOT.'/config/db.inc.php');
}
else
{
    define('DB_HOST',       '127.0.0.1');
    define('DB_NAME',       'menudb');
    define('DB_USERNAME',   'henry');
    define('DB_PASSWORD',   'henry');
}

define('SQL_AES_KEY',   'tr0nL3g@c4');

if (file_exists(ROOT.'/config/paths.inc.php'))
{
    require_once(ROOT.'/config/paths.inc.php');
}
else
{
    define('OS_PATH_PUBLIC',        ROOT . DS . 'public');
    define('OS_IMAGE_PATH',         ROOT . DS . 'public' .  DS . 'img');

    define('OS_ASSET_PATH',         '/home/custom_code');
    define('OS_UPLOAD_PATH',        OS_ASSET_PATH.'/www.uploads');
    define('OS_PURGE_PATH',         OS_ASSET_PATH.'/www.purge');
    define('OS_MENU_PATH',          OS_ASSET_PATH.'/www.menus');
    define('OS_TEMP_PATH',          OS_ASSET_PATH.'/www.temp');

    define('OS_DEFAULT_NO_IMAGE_PATH',      OS_IMAGE_PATH);
    define('OS_DEFAULT_NO_IMAGE_FILE',      'noimage.jpg');
    define('OS_DEFAULT_NO_IMAGE_WIDTH',     258);
    define('OS_DEFAULT_NO_IMAGE_HEIGHT',    196);

    define('OS_DEFAULT_ERROR_PAGE_PATH',    OS_PATH_PUBLIC.'/pages');
}

define('WEB_PATH_CSS',      DS . 'css');
define('WEB_PATH_JS',       DS . 'js');
define('WEB_PATH_OTHER',    DS . 'other');

define('PASSCODE_TROJAN',   'locomotion');

define('GOOGLE_API_KEY', 'AIzaSyDPez_dxVdHnZM8COpU4-Hs3qKxTFE0vKM');
