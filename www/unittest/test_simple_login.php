<?php

require_once 'ut_login.php';
require_once 'ut_gohome.php';

$login = new ut_login(true);
$login->run();

$home = new ut_gohome();
$home->run();
