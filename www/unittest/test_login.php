<?php

$login = new ut_login();

echo 'Test good login...';
$login->set('lu', 'henry');
$login->set('lp', 'henry');

$res = $login->run();
if ($res)
    echo 'Successful!';
else
    echo 'Failed!';

echo "\n\n";

echo 'Test bad login...';
$login->bad_login();
$login->set('lu', 'henry');
$login->set('lp', 'henry bad');

$res = $login->run();
if ($res)
    echo 'Successful!';
else
    echo 'Failed!';

