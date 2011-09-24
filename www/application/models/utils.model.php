<?php

class UtilsModel
{
    static function normalizeUrl($url)
    {
        $url = trim($url);

        if (empty($url))
            return '';

        $scheme = parse_url($url, PHP_URL_SCHEME);

        if (empty($scheme))
            $url = 'http://'.$url;

        return $url;
    }

    static function getCurrentUserId()
    {
        if (!isset($_SESSION['id']))
            return false;

        return $_SESSION['id'];
    }

    static function getCurrentPermissions()
    {
        if (!$_SESSION['perms'])
            return false;
        return $_SESSION['perms'];
    }

    static function clearCurrentPermissions()
    {
        unset($_SESSION['perms']);
    }
}
