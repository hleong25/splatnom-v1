<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(dirname(__FILE__))));

$root_path = ROOT;

// load the config file for DB credentials
require_once("$root_path/config/config.php");

// load the model and util class
require_once("$root_path/library/model.class.php");
require_once("$root_path/application/utils/util.php");

// finally... load the email class handler
require_once("$root_path/application/models/mail.model.php");


$model = new MailModel;

// process the unsent emails
$ts_start = microtime(true);
$stats = $model->process_queue();
$ts_diff = microtime(true) - $ts_start;

$total = $stats['fail'] + $stats['sent'];

if ($total > 0)
{
    date_default_timezone_set('UTC');
    printf('[%s] Sent: %d, Fail: %d, Elapsed Time: %0.3f'."\n",
           date('r'),
           $stats['sent'],
           $stats['fail'],
           $ts_diff
          );
}
