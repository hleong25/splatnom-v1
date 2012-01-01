<?php

require_once('../config/config.php');

abstract class UnitTest
{
    private $m_curl_handle = false;
    private $m_curl_exec = '';
    private $m_curl_getinfo = '';

    //protected function is_new_session(); // { return false };
    abstract protected function getUrl();
    abstract protected function getParams();
    abstract protected function validate();

    public function __construct($new_session = false)
    {
        $cookie_file = OS_TEMP_PATH.'/unit_test.cookie.txt';

        $url = $this->getUrl();

        //open connection
        $ch = curl_init();

        // setup the session cookie -- so we can keep being logged in
        if ($new_session)
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        else
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

        // setup curl
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $this->m_curl_handle = $ch;
    }

    public function __destruct()
    {
        curl_close($this->get_ch());
    }

    public function get_ch()
    {
        return $this->m_curl_handle;
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
        $ch = $this->get_ch();

        $fields = $this->getParams();
        $post = $this->toPostStr($fields);

        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $this->m_curl_exec = curl_exec($ch);
        $this->m_curl_getinfo = curl_getinfo($ch);

        $this->validate();
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
}
