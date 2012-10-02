<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(dirname(__FILE__))));

$root_path = ROOT;

// load the config file for DB credentials
require_once("$root_path/config/config.php");

// load the model class
require_once("$root_path/library/model.class.php");

// finally... load the email class handler
require_once("$root_path/application/models/mail.model.php");


$model = new MailModel;
for ($ii = 0, $jj = 6; $ii < $jj; ++$ii)
{
    if ($ii > 0)
    {
        sleep(8);
    }

    // process the unsent emails
    $model->process_queue();
}
