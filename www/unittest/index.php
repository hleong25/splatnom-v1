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
    $unit_tests[] = 'test_login';
    $unit_tests[] = 'test_new_menu';

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
    $file = "{$test}.php";

    echo<<<EOHTML
        <a href="/unittest/">go main</a>
        <br/>
EOHTML;

    echo '<pre>';
    require_once($file);
    echo '<pre>';
}

function logit($obj, $file=null, $line=null)
{
    $from = "";
    if (!empty($file) && !empty($line))
        $from = "({$file}:{$line}): ";

    if (is_string($obj))
        echo ($from.$obj)."\n";
    else
        echo ($from.var_export($obj, true))."\n";
}

function __autoload ($className)
{
    $clsName = strtolower($className);

    $load_file = "./ut/{$clsName}.php";

    if (!file_exists($load_file))
    {
        error_log("unit_test file '{$load_file}' does not exists for class '{$className}'");
        return;
    }

    require_once($load_file);
}
