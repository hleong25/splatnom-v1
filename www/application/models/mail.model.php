<?php
require_once('Mail.php');
require_once('Mail/mime.php');

class MailModel
    extends Model
{
    public $DEFAULT_EMAIL = 'hanksmenu@gmail.com';

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

        logit($send);

        if ($send !== true)
            logit($send);

        return $send;
    }

    function send_smtp($from, $to, $subject, $message)
    {
        $bUseSSL = true;
        $crlf = "\n";

        if (empty($from))
            $from = $this->DEFAULT_EMAIL;

        $headers = array(
            'From' => $from,
            'Return-Path' => $from,
            'Subject' => $subject
        );

        if ($bUseSSL == true)
        {
            $host = 'ssl://smtp.gmail.com';
            $port = '465';
        }
        else
        {
            $host = 'smtp.gmail.com';
            $port = '25';
        }

        $username = 'hanksmenu@gmail.com';
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

        if ($send !== true)
            logit('Mail error: '.$send->getMessage());

        return $send;
    }

    function send_tester($from, $to, $subject, $message)
    {
        $send = $this->send_smtp($from, $to, $subject, $message);

        return $send === true;
    }
}
