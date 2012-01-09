<?php

define('OS_TEMP_PATH', '/home/custom_code/www.temp');
define('COOKIE_FILE', OS_TEMP_PATH.'/unit_test.cookie.txt');

abstract class Unit_Test
{
    private $m_curl_errno = 0;
    private $m_curl_error = '';
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

    protected function get_curl_exec()
    {
        return $this->m_curl_exec;
    }

    protected function get_curl_getinfo()
    {
        return $this->m_curl_getinfo;
    }

    public function run($verbose = false)
    {
        $cookie_file = COOKIE_FILE;

        $url = $this->getUrl();

        if ($verbose)
        {
            logit($url);
            logit($this->m_params);
        }

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

        $postdata = $this->m_params;
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

        $this->m_curl_exec = curl_exec($ch);
        $this->m_curl_getinfo = curl_getinfo($ch);

        $this->m_curl_errno = curl_errno($ch);
        $this->m_curl_error = curl_error($ch);

        if ($this->m_curl_errno != 0)
        {
            $curl_error = "cURL errno({$this->m_curl_errno}): {$this->m_curl_error}";
            logit($curl_error);
            return false;
        }

        $validate = $this->validate();

        curl_close($ch);

        return $validate;
    }

    public function set($key, $value)
    {
        if (!is_array($value))
        {
            $this->m_params[$key] = $value;
        }
        else
        {
            unset($this->m_params[$key]);

            foreach ($value as $idx => $val)
            {
                $new_key = "{$key}[{$idx}]";
                $this->m_params[$new_key] = $val;
            }
        }
    }

    public function setParams($key, $value)
    {
        $this->set($key, $value);
    }
}
