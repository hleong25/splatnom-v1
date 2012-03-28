<?php
require_once('lessc.inc.php');

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

        $this->m_res['css'] = array();
        $this->m_res['js'] = array();
        $this->m_res['remotejs'] = array();

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

        // add jquery
        $this->addJs('jquery-1.7.2.min', WEB_PATH_OTHER);

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
            /*
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
            */

            $file = OS_PATH_PUBLIC . DS . $value . '.css';
        }
        else if ($type === 'js')
        {
            $file = OS_PATH_PUBLIC . DS . $value . '.js';
        }
        else if ($type === 'remotejs')
        {
            // do nothing
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
        // always try to compile the LESS to CSS first, if it exists
        if (DEVELOPMENT_ENVIRONMENT && ($path === WEB_PATH_CSS))
        {
            $compiled_css = $this->auto_compile_less($css, $path);

            if ($compiled_css !== true)
            {
                Util::logit("Failed to compile LESS file: {$css}", __FILE__, __LINE__);
                return;
            }
        }

        $this->addResource('css', $path . DS . $css, $bCheckIfExists);
    }

    function addJs($js, $path = WEB_PATH_JS, $bCheckIfExists = true)
    {
        $this->addResource('js', $path . DS . $js, $bCheckIfExists);
    }

    function addRemoteJs($js)
    {
        $this->addResource('remotejs', $js, false);
    }

    function auto_compile_less($less, $path = WEB_PATH_CSS)
    {
        $fname = OS_PATH_PUBLIC.$path.DS.$less;

        $less_fname = $fname.'.less';
        $css_fname  = $fname.'.css';

        if (!file_exists($less_fname))
        {
            Util::logit("LESS file not found: {$less_fname}", __FILE__, __LINE__);
            return false;
        }

        // load the cache
        $cache_fname = $less_fname.'.cache';
        if (file_exists($cache_fname))
        {
            //Util::logit("CSS cache exists: $css_fname");
            $cache = unserialize(file_get_contents($cache_fname));
        }
        else
        {
            //Util::logit("CSS cache does not exists: $css_fname");
            $cache = $less_fname;
        }

        $new_cache = lessc::cexecute($cache);
        if (!is_array($cache) || $new_cache['updated'] > $cache['updated'])
        {
            //Util::logit("CSS cache is old, replacing it: $css_fname");
            file_put_contents($cache_fname, serialize($new_cache));
            file_put_contents($css_fname, $new_cache['compiled']);
        }

        return true;
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
        //$this->addCss('default.jquery-ui');
    }

    function addAddThis()
    {
        // NOTE: www.addthis.com login is hleong25+addthis@gmail.com

        global $get_url;
        $site = Util::getTopLevelDomain();

        $url = "http://{$site}/{$get_url}";
        $title = '';
        $desc = '';

        if (isset($this->m_variables['meta_url']))
            $url = $this->m_variables['meta_url'];

        if (isset($this->m_variables['meta_title']))
            $title = $this->m_variables['meta_title'];

        if (isset($this->m_variables['meta_desc']))
            $desc = $this->m_variables['meta_desc'];

        $html=<<<EOHTML
            <div class="myaddthis clearfix addthis_toolbox addthis_default_style "
                addthis:url="{$url}"
                addthis:title="splatnom wants to tell you about '{$title}'"
                addthis:description="{$desc}"
            >
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

    function getCss()
    {
        $css = array_unique($this->m_res['css']);
        return $css;
    }

    function getJs()
    {
        $js = array_unique($this->m_res['js']);
        return $js;
    }

    function getRemoteJs()
    {
        $js = array_unique($this->m_res['remotejs']);
        return $js;
    }

    function getNavLinks()
    {
        return $this->m_nav;
    }
}
