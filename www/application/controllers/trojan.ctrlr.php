<?php
require_once('PEAR/Registry.php');

class TrojanController
    extends Controller
{
    function __construct ($base_name, $action)
    {
        parent::__construct($base_name, $action);

        $this->m_bRender = false;
    }

    function onAction_init_admin()
    {
        $rst = $this->Trojan->init_admin();
        $this->redirect('/home/main');
    }

    function onAction_check_system()
    {
        $system = array();

        $check_paths = array
        (
            OS_UPLOAD_PATH,
            OS_PURGE_PATH,
            OS_MENU_PATH,
            OS_TEMP_PATH,
        );

        foreach ($check_paths as $path)
        {
            if (!file_exists($path))
            {
                $system[] = array(
                    'req' => "Path '{$path}' not found",
                    'fix' => 'Create it',
                    'hint' => "mkdir -p \"{$path}\""
                );
            }
            else if (!is_writable($path))
            {
                $system[] = array(
                    'req' => "Path '{$path}' not writable",
                    'fix' => 'Make it writable',
                    'hint' => "chmod 0777 \"{$path}\""
                );
            }
        }

        if (!function_exists('gd_info'))
        {
            $system[] = array(
                'req' => 'gd library',
                'fix' => 'install php-gd',
                'hint' => 'yum install php-gd',
            );
        }

        if (!function_exists('json_encode'))
        {
            $system[] = array(
                'req' => 'json_encode/json_decode',
                'fix' => 'install json support',
                'hint' => 'pear install json (???) or have PHP >= 5.2.0',
            );
        }

        if (!class_exists('ZipArchive', false))
        {
            $system[] = array(
                'req' => 'ZipArchive',
                'fix' => 'install php zip support',
                'hint' => 'yum install php-pecl-zip or have PHP >= 5.2.0',
            );
        }

        if (getEnv('browser_type') === false)
        {
            $system[] = array(
                'req' => 'getEnv(browser_type)',
                'fix' => 'Add "browser sniffing" to htaccess',
                'hint' => 'http://v4.thewatchmakerproject.com/blog/no-more-css-hacks-browser-sniffing-with-htaccess/',
            );
        }

        if (!class_exists('DOMDocument', false))
        {
            $system[] = array(
                'req' => 'DOMDocument',
                'fix' => 'install php xml',
                'hint' => 'yum install php-xml',
            );
        }

        $pear = new PEAR_Registry;

        if (!$pear->packageExists('Mail'))
        {
            $system[] = array(
                'req' => 'Mail',
                'fix' => 'install php pear Mail support',
                'hint' => 'pear install Mail',
            );
        }

        if (!$pear->packageExists('Mail_Mime'))
        {
            $system[] = array(
                'req' => 'Mail_Mime',
                'fix' => 'install php pear Mail_Mime support',
                'hint' => 'pear install Mail_Mime',
            );
        }

        if (!$pear->packageExists('Net_SMTP'))
        {
            $system[] = array(
                'req' => 'Mail',
                'fix' => 'install php pear Net_SMTP support',
                'hint' => 'pear install Net_SMTP',
            );
        }

        if (!function_exists('curl_init'))
        {
            $system[] = array(
                'req' => 'curl lib',
                'fix' => 'install php curl library support',
                'hint' => 'apt-get install php-curl',
            );
        }

        if (!empty($system))
        {
            $this->m_bRender = true;
            $this->set('system', $system);
        }
        else
        {
            $this->redirect('/home/main');
        }
    }
}

