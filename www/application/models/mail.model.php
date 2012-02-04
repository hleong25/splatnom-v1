<?php
require_once('Mail.php');
require_once('Mail/mime.php');

class MailModel
    extends Model
{
    public $DEFAULT_EMAIL = 'support@splatnom.com';

    function send($from, $to, $subject, $message)
    {
        $crlf = "\n";

        $headers = array(
            'From' => $from,
            'Return-Path' => $from,
            'Subject' => $subject
        );

        $mail =& Mail::factory('mail');
        $mime = new Mail_mime($crlf);

        $mime->setHTMlBody($message);
        $body = $mime->get();
        $headers = $mime->headers($headers);

        $send = $mail->send($to, $headers, $body);

        Util::logit($send);

        if ($send !== true)
            Util::logit($send);

        return $send;
    }

    function send_smtp($from, $to, $subject, $message)
    {
        $crlf = "\n";

        if (empty($from))
            $from = $this->DEFAULT_EMAIL;

        $headers = array(
            'From' => "{$from} <{$from}>",
            'To' => "{$to} <{$to}>",
            'Return-Path' => $from,
            'Subject' => $subject
        );

        $host = 'ssl://smtp.gmail.com';
        $port = '465';

        $username = 'support@splatnom.com';
        $password = 'alhambra1234';

        $smtp_cfg = array();
        $smtp_cfg['host'] = $host;
        $smtp_cfg['port'] = $port;
        $smtp_cfg['auth'] = true;
        $smtp_cfg['username'] = $username;
        $smtp_cfg['password'] = $password;

        $mail = Mail::factory('smtp', $smtp_cfg);
        $mime = new Mail_mime($crlf);

        $mime->setHTMlBody($message);
        $body = $mime->get();
        $headers = $mime->headers($headers);

        $send = $mail->send($to, $headers, $body);

        if (PEAR::isError($send))
        {
            Util::logit('Mail error: '.$send->getMessage(), __FILE__, __LINE__);
            return false;
        }

        return true;
    }

    function send_tester($from, $to, $subject, $message)
    {
        $send = $this->send_smtp($from, $to, $subject, $message);

        return $send === true;
    }

    function grab_data($ctrl, $page, $params)
    {
        $view_page = $ctrl . DS . $page . '.php';
        $path = ROOT . DS . 'application' . DS . 'views';
        $file = $path . DS . $view_page;

        if (!file_exists($file))
        {
            Util::logit("Email view page not found: {$view_page}");
            return false;
        }

        ob_start();
            extract($params);
            include($file);
        $data = ob_get_clean();

        return $data;
    }
}
