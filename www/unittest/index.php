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
    $files = scandir('.');

    foreach ($files as $file)
    {
        if (stripos($file, 'test_') !== 0)
            continue;

        echo<<<EOHTML
            <a href="/unittest/index.php?test={$file}">{$file}</a>
            <br/>
EOHTML;
    }
}

function exec_test($file)
{
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

    $msg = '';
    if (is_string($obj))
        $msg = ($from.$obj)."\n";
    else
        $msg = ($from.var_export($obj, true))."\n";

    echo htmlentities($msg);
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
