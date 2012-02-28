<?php

class Template
{
    protected $m_variables = array();
    protected $m_base_name;
    protected $m_action;

    protected $m_nav = array();

    protected $m_res = array();

    function __construct ($base_name, $action)
    {
        $this->m_base_name = strtolower($base_name);
        $this->m_action = $this->fixAction($action);

        $this->init();
    }

    function fixAction($action)
    {
        $preStr = 'onAction_';
        $preStrLen = strlen($preStr);

        if (strcmp(substr($action, 0, $preStrLen), $preStr) === 0)
        {
            $new_action = substr($action, $preStrLen);
        }
        else
        {
            $new_action = $action;
        }

        return $new_action;
    }

    function init()
    {
        global $get_url;

        $this->set('myurl', $get_url);

        $this->addCss('reset');
        $this->addCss('default');

        // add jquery
        $this->addJs('jquery-1.7.1.min', WEB_PATH_OTHER);

        $this->setupNav();
    }

    function setupNav()
    {
        $links = array();

        $links[] = array('css' => 'nav ', 'lbl' => 'new menu', 'lnk' => 'menu/new');

        if (!Util::getUserId())
        {
            global $get_url;

            $goto_url = '&goto='.$get_url;

            // not logged in
            $links[] = array('css' => 'nav new_user', 'lbl' => 'register', 'lnk' => 'user/register');
            $links[] = array('css' => 'nav login', 'lbl' => 'login', 'lnk' => "login/main{$goto_url}");
        }
        else
        {
            // logged in
            if(Util::getPermissions('admin'))
                $links[] = array('css' => 'nav ', 'lbl' => 'admin', 'lnk' => 'admin/main');

            $links[] = array('css' => 'nav', 'lbl' => 'profile', 'lnk' => 'user/profile');
            $links[] = array('css' => 'nav logoff', 'lbl' => 'logoff', 'lnk' => 'login/end');
        }

        foreach ($links as $lnk)
        {
            $this->m_nav[] = $lnk;
        }
    }

    /** Set Variables * */
    function set ($name, $value)
    {
        $this->m_variables[$name] = $value;
    }

    /** Display Template * */
    function render ($bRender)
    {
        extract($this->m_variables);

        $header = ROOT . DS . 'application' . DS . 'views' . DS . $this->m_base_name . DS . 'header.php';
        if (!file_exists($header))
        {
            $header = ROOT . DS . 'application' . DS . 'views' . DS . 'header.php';
        }

        $footer = ROOT . DS . 'application' . DS . 'views' . DS . $this->m_base_name . DS . 'footer.php';
        if (!file_exists($footer))
        {
            $footer = ROOT . DS . 'application' . DS . 'views' . DS . 'footer.php';
        }

        $body = ROOT . DS . 'application' . DS . 'views' . DS . $this->m_base_name . DS . $this->m_action . '.php';

        if (!file_exists($body))
        {
            Util::logit("Render failed. File not found: {$body}");
            return;
        }

        if ($bRender)
        {
            include($header);
            include($body);
            include($footer);
        }
        else
        {
            include($body);
        }
    }

    function getBrowserVersion()
    {
        // NOTE: getEnv('browser_type') is set in the .htaccess file
        //       it's a hack to get the browser info

        // Note: this will handle different browser version types
        //       the output will be the browser specific version
        $browser = strtolower(getEnv('browser_type'));
        return $browser;

    }

    function addResource($type, $value, $bCheckIfExists = true)
    {
        $file = false;
        if ($type === 'css')
        {
            $browser_type = $this->getBrowserVersion();
            if (!empty($browser_type))
            {
                $file = OS_PATH_PUBLIC . DS . $value . ".{$browser_type}.css";
                if (file_exists($file))
                {
                    // since the file exists, we're gonna use this browser specific file
                    $value .= ".{$browser_type}";
                }
                else
                {
                    // since the file exists does not exists, we'll go use the default one below
                    $file = false;
                }
            }

            if (empty($file))
                $file = OS_PATH_PUBLIC . DS . $value . '.css';
        }
        else if ($type === 'js')
        {
            $file = OS_PATH_PUBLIC . DS . $value . '.js';
        }

        if ($bCheckIfExists && !file_exists($file))
        {
            Util::logit("(Template.class) File does not exists '{$file}'");
            //return;
        }

        $this->m_res[$type][] = $value;
    }

    function addCss($css, $path = WEB_PATH_CSS, $bCheckIfExists = true)
    {
        $this->addResource('css', $path . DS . $css, $bCheckIfExists);
    }

    function addJs($js, $path = WEB_PATH_JS, $bCheckIfExists = true)
    {
        $this->addResource('js', $path . DS . $js, $bCheckIfExists);
    }

    function addJqueryUi()
    {
        $version = '1.8.16';
        $theme = 'redmond';

        $path = "jquery-ui/jquery-ui-{$version}.{$theme}";
        $css  = "{$path}/css/{$theme}/jquery-ui-{$version}.custom";
        $js   = "{$path}/js/jquery-ui-{$version}.custom.min";

        $this->addCss($css, WEB_PATH_OTHER);
        $this->addJs($js, WEB_PATH_OTHER);

        // Add default jquery-ui CSS
        $this->addCss('default.jquery-ui');
    }

    function addAddThis()
    {
        // NOTE: www.addthis.com login is hleong25+addthis@gmail.com

        $addThis_services = array(
            'facebook',
            'twitter',
            'reddit',
            'email',
            'google_plusone',
        );

        $html=<<<EOHTML
            <div class="addthis_toolbox addthis_default_style ">
                <a class="addthis_button_facebook"></a>
                <a class="addthis_button_twitter"></a>
                <a class="addthis_button_google_plusone" g:plusone:count="false"></a>
                <a class="addthis_button_reddit"></a>
                <a class="addthis_button_email"></a>
                <a class="addthis_button_compact"></a>
            </div>
EOHTML;

        echo $html;

        $js = 'http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4f2631133193ecb7';
        $this->addResource('js', $js, false);
    }

    function includeCss()
    {
        foreach ($this->m_res['css'] as $css)
        {
            printf('<link rel="stylesheet" href="%s.css" />', $css);
        }
    }

    function includeJs()
    {
        foreach ($this->m_res['js'] as $js)
        {
            printf('<script type="text/javascript" src="%s.js"></script>', $js);
        }
    }

    function includeNavLinks()
    {
        $bCont = false;

        foreach ($this->m_nav as $lnk)
        {
            if ($bCont)
            {
                printf('<span class="lnkspc"> | </span>');
            }

            printf('<a class="%s" href="%s">%s</a>', $lnk['css'], DS . $lnk['lnk'], $lnk['lbl']);

            $bCont = true;
        }

    }
}
