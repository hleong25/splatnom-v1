<?php

class Controller
{
	protected $m_model;
	protected $m_controller;
	protected $m_action;
	protected $m_template;

    protected $m_bRender = true;
    protected $m_bRedirect = false;

	function __construct($base_name, $action)
    {
		$this->m_controller = $base_name . 'Controller';
		$this->m_model = $base_name . 'Model';
		$this->m_action = $action;

		$this->$base_name = new $this->m_model;
		$this->m_template = new Template($base_name, $action);
	}

	function set($name, $value)
    {
		$this->m_template->set($name, $value);
	}

    function override_body_page_name($new_page)
    {
        return $this->m_template->override_body_page_name($new_page);
    }

	function __destruct()
    {
        if (!isset($this->m_template))
        {
            return;
        }

        if (!$this->m_bRedirect)
            $this->m_template->render($this->m_bRender);

        $exec_time = scriptExecutionTime();
        $max_exec_time = 2.0;

        if ($exec_time > $max_exec_time)
        {
            global $get_url;
            $msg = sprintf('Execution time: %.03f/%0.3f for %s', $exec_time, $max_exec_time, $get_url);
            Util::logit($msg);
        }
	}

    function error_page($code=404)
    {
        $this->m_bRedirect = true;
        Util::error_page($code);
    }

    function redirect($location)
    {
        $this->m_bRedirect = true;
        header("Location: {$location}");
    }

    function addCss($css, $path = WEB_PATH_CSS)
    {
        $this->m_template->addCss($css, $path);
    }

    function addJs($js, $path = WEB_PATH_JS)
    {
        $this->m_template->addJs($js, $path);
    }

    function addRemoteJs($js)
    {
        $this->m_template->addRemoteJs($js);
    }

    function addJqueryUi()
    {
        $this->m_template->addJqueryUi();
    }

}
