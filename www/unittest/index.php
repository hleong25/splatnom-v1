<?php

if (empty($_GET))
{
    show_tests();
}
else
{
    $test = $_GET['test'];
    exec_test($test);
}

function logit($obj, $file=null, $line=null)
{
    $from = "";
    if (!empty($file) && !empty($line))
        $from = "({$file}:{$line}): ";

/*
    if (is_string($obj))
        error_log($from.$obj);
    else
        error_log($from.var_export($obj, true));
*/

    if (is_string($obj))
        echo ($from.$obj);
    else
        echo ($from.var_export($obj, true));
}

function toPostStr($params)
{
    $fields_string = '';
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string,'&');

    return $fields_string;
}

function show_tests()
{
    $unit_tests[] = 'login';

    foreach ($unit_tests as $ut)
    {
        echo<<<EOHTML
            <a href="/unittest/index.php?test={$ut}">{$ut}</a>
            <br/>
EOHTML;
    }
}

function exec_test($test)
{
    $ut_file = "ut_{$test}.php";

    echo '<pre>';
    require_once($ut_file);
    echo '<pre>';
}
