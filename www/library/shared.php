<?php

/** Check if environment is development and display errors * */
function setReporting ()
{
    if (DEVELOPMENT_ENVIRONMENT == true)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');
    }
    else
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 'Off');
//        ini_set('log_errors', 'On');
//        ini_set('error_log', ROOT . DS . 'tmp' . DS . 'logs' . DS . 'error.log');
    }
}

/** Check for Magic Quotes and remove them * */
function stripSlashesDeep ($value)
{
    $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
    return $value;
}

function removeMagicQuotes ()
{
    if (get_magic_quotes_gpc())
    {
        $_GET = stripSlashesDeep($_GET);
        $_POST = stripSlashesDeep($_POST);
        $_COOKIE = stripSlashesDeep($_COOKIE);
    }
}

/** Check register globals and remove them * */
function unregisterGlobals ()
{
    if (ini_get('register_globals'))
    {
        $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
        foreach ($array as $value)
        {
            foreach ($GLOBALS[$value] as $key => $var)
            {

                if ((isset($GLOBALS[$key])) && ($var === $GLOBALS[$key]))
                {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }
}

/** Main Call Function * */
function callHook ()
{
    global $get_url;

    $urlArray = array();
    $urlArray = explode("/", $get_url);

/*
    $name = $urlArray[0];
    array_shift($urlArray);
    $action = 'onAction_' . @$urlArray[0];
    array_shift($urlArray);
    $queryString = $urlArray;
*/

    $name = array_shift($urlArray);
    $action = 'onAction_' . array_shift($urlArray);
    $queryString = $urlArray;

    $name = ucwords(trim($name));
    $ctrl = $name . 'Controller';

    if (!class_exists($ctrl))
    {
        error_log("Class '$ctrl' does not exists");
    }
    else
    {
        // load user permissions if needed
        if ( (isset($_SESSION['id']) === true) &&
             (isset($_SESSION['perms']) === false) )
        {
            $user = new UserModel();
            $_SESSION['perms'] = $user->getUserPermission($_SESSION['id']);
        }

        // make sure admin pages are only accessed by admins
        if ((strtolower($name) == 'admin') &&
            (!isset($_SESSION['perms']) || ($_SESSION['perms']['admin'] != true)))
        {
            Util::redirect('/home/main');
            return;
        }

        $dispatch = new $ctrl($name, $action);
        if (method_exists($ctrl, $action))
        {
            // unseo query string
            foreach ($queryString as &$qs)
            {
                $unseo = Util::getUnseo($qs);
                $qs = array_shift($unseo);
            }

//            call_user_func_array(array($dispatch, $action), array($queryString));
            call_user_func_array(array($dispatch, $action), $queryString);
        }
        else
        {
            error_log("Function call '$ctrl::$action' does not exists");
        }
    }
}

function strEndsWith($haystack, $needle, &$parsed)
{
    $hslen = strlen($haystack);
    $nlen = strlen($needle);

    if ($nlen > $hslen)
        return false;

    $idx = substr_compare($haystack, $needle, -$nlen, true);

    if ($idx !== 0)
        return false;

    $parsed = substr($haystack, 0, ($hslen - $nlen));

    return true;
}

function scriptExecutionTime()
{
    global $__page_start_time;
    $now = microtime(true);
    return $now - $__page_start_time;
}

/** Autoload any classes that are required * */
function __autoload ($className)
{
    $clsName = strtolower($className);
    $load_file = false;
    $base_name = false;

    if ((strcmp($clsName, 'controller') === 0) ||
        (strcmp($clsName, 'model') === 0) ||
        (strcmp($clsName, 'template') === 0))

    {
        $load_file = ROOT . DS . 'library' . DS . $clsName . '.class.php';
    }
    else if (strEndsWith($clsName, 'controller', $base_name))
    {
        $load_file = ROOT . DS . 'application' . DS . 'controllers' . DS . $base_name . '.ctrlr.php';
    }
    else if (strEndsWith($clsName, 'model', $base_name))
    {
        $load_file = ROOT . DS . 'application' . DS . 'models' . DS . $base_name . '.model.php';
    }
    else if (strEndsWith($clsName, 'util', $base_name))
    {
        if (empty($base_name))
            $load_file = ROOT . DS . 'application' . DS . 'utils' . DS . 'util.php';
        else
            $load_file = ROOT . DS . 'application' . DS . 'utils' . DS . $base_name . '.util.php';
    }

    if (!$load_file)
    {
        error_log("__autoload failed with '{$className}'");
        return;
    }

    if (!file_exists($load_file))
    {
        error_log("Controller file '{$load_file}' does not exists for class '{$className}'");
        return;
    }

    require_once($load_file);
}

setReporting();
removeMagicQuotes();
unregisterGlobals();
callHook();
