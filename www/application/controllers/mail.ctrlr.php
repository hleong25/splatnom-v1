<?php

class MailController
    extends Controller
{
    function onAction_sent()
    {
        $this->addCss('mail/mail.sent');
        //$this->addJs('mail/mail.sent');
    }

    function onAction_tester()
    {
        $this->addCss('mail/mail.tester');

        $this->addJs('new.jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('mail/mail.tester');

        if (empty($_POST))
            return;

        $to = $_POST['to'];
        $subject = $_POST['subject'];
        $message = ($_POST['message']);

        $this->set('to', $to);
        $this->set('subject', $subject);
        $this->set('message', $message);

        $mail_id = $this->Mail->queue(null, $to, $subject, $message);
        if (empty($mail_id))
        {
            $this->set('err', 'Error queuing email. Try again');
        }
        else
        {
            $this->redirect('/mail/sent');
        }
    }

    function onAction_henry()
    {
        $this->m_bRender = false;

        $user = 'henry_username';
        $verifyCode = 'myverifycode';
        $emailTo = 'hleong25@gmail.com';

        $mail = new MailModel();
        $subject = 'Verify account for splatnom';

        $params = array(
            'user' => $user,
            'verifyCode' => $verifyCode,
        );

        $msg = $mail->grab_data('user', 'email_verification', $params);
        if (empty($msg))
            return false;

        $sent = $mail->queue(null, $emailTo, $subject, $msg);

        return $sent;
    }
}
