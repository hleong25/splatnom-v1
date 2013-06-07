<?php

define('OS_PATH_PUBLIC',        ROOT . DS . 'public');
define('OS_IMAGE_PATH',         ROOT . DS . 'public' .  DS . 'img');

define('OS_ASSET_PATH',         '/home/custom_code');
define('OS_UPLOAD_PATH',        OS_ASSET_PATH.'/www.uploads');
define('OS_PURGE_PATH',         OS_ASSET_PATH.'/www.purge');
define('OS_MENU_PATH',          OS_ASSET_PATH.'/www.menus');
define('OS_TEMP_PATH',          OS_ASSET_PATH.'/www.temp');
define('OS_EVENT_PATH',         OS_ASSET_PATH.'/www.events');

define('OS_DEFAULT_NO_IMAGE_PATH',      OS_IMAGE_PATH);
define('OS_DEFAULT_NO_IMAGE_FILE',      'noimage.jpg');
define('OS_DEFAULT_NO_IMAGE_WIDTH',     258);
define('OS_DEFAULT_NO_IMAGE_HEIGHT',    196);

define('OS_DEFAULT_ERROR_PAGE_PATH',    OS_PATH_PUBLIC.'/pages');
