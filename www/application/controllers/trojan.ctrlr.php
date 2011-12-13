<?php

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

        if (PHP_VERSION_ID >= 50202)
        {
            $system[] = array(
                'new_req' => 'date(u) supported',
                'fix' => 'change all dates with u support ',
            );
        }

        $check_paths = array
        (
            OS_PATH_PUBLIC,
            OS_UPLOAD_PATH,
            OS_PURGE_PATH,
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
                'fix' => 'install php-gd on centos',
                'hint' => 'yum install php-gd',
            );
        }

        if (!function_exists('json_encode'))
        {
            $system[] = array(
                'req' => 'json_encode/json_decode',
                'fix' => 'install json support on centos',
                'hint' => 'pear install json (???) or have PHP >= 5.2.0',
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

