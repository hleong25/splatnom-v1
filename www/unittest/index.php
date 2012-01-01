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

function show_tests()
{
    $unit_tests[] = 'test_simple_login';

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
    require_once('unit_test.php');

    $file = "{$test}.php";

    echo<<<EOHTML
        <a href="/unittest/">go main</a>
        <br/>
EOHTML;

    echo '<pre>';
    require_once($file);
    echo '<pre>';
}
