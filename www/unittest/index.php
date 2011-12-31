<?php

$unit_tests[] = 'ut_login';

foreach ($unit_tests as $ut)
{
    $file = $ut.'.php';
    require_once($file);
}
