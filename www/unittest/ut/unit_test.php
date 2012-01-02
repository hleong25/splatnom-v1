<?php

define('OS_TEMP_PATH', '/home/custom_code/www.temp');
define('COOKIE_FILE', OS_TEMP_PATH.'/unit_test.cookie.txt');

abstract class Unit_Test
{
    private $m_curl_exec = '';
    private $m_curl_getinfo = '';

    protected $m_params = array();

    abstract protected function getUrl();
    abstract protected function validate();

    public function __construct()
    {
    }

    public function __destruct()
    {
        // empty
    }

    protected function isNewSession()
    {
        return false;
    }

    public function get_curl_exec()
    {
        return $this->m_curl_exec;
    }

    public function get_curl_getinfo()
    {
        return $this->m_curl_getinfo;
    }

    public function run()
    {
        $cookie_file = COOKIE_FILE;

        $url = $this->getUrl();

        //open connection
        $ch = curl_init();

        // setup the session cookie -- so we can keep being logged in
        if ($this->isNewSession())
            @unlink(COOKIE_FILE);

        if ($this->isNewSession())
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        else
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

        // setup curl
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // set the POST params
        $fields = $this->getParams();
        $post = $this->toPostStr($fields);

        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $this->m_curl_exec = curl_exec($ch);
        $this->m_curl_getinfo = curl_getinfo($ch);

        $validate = $this->validate();

        curl_close($ch);

        return $validate;
    }

    protected function logit($obj, $file=null, $line=null)
    {
        $from = "";
        if (!empty($file) && !empty($line))
            $from = "({$file}:{$line}): ";

        if (is_string($obj))
            echo ($from.$obj)."\n";
        else
            echo ($from.var_export($obj, true))."\n";
    }

    protected function toPostStr($params)
    {
        $fields_string = '';
        foreach ($params as $key=>$value)
        {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string,'&');

        return $fields_string;
    }

    public function set($key, $value)
    {
        $this->m_params[$key] = $value;
    }

    public function setParams($key, $value)
    {
        $this->set($key, $value);
    }

    public function getParams()
    {
        return $this->m_params;
    }
}
