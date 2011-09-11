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
        $this->addJs('mail/mail.tester');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

        if (empty($_POST))
            return;

        $to = $_POST['to'];
        $subject = $_POST['subject'];
        $message = ($_POST['message']);

        $this->set('to', $to);
        $this->set('subject', $subject);
        $this->set('message', $message);

        $sent = $this->Mail->send_tester(null, $to, $subject, $message);
        if ($sent !== true)
        {
            $this->set('err', 'Error sending mail. Try again');
        }
        else
        {
            $this->redirect('/mail/sent');
        }
    }

}
