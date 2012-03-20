<?php

class HomeController
    extends Controller
{
    function onAction_main()
    {
        $this->addJqueryUi();
        //$this->addCss('table');
        //$this->addCss('home/home');
        $this->addLess('home/home.main');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('home/home');

        $loc = Util::cookie('location');
        if (!empty($loc))
        {
            $this->set('location', $loc);
        }

        $this->set('ready_menus', $this->Home->getReadyMenus());

        $bAdmin = Util::getPermissions('admin');
        $this->set('is_admin', $bAdmin);

        $bMetadata = Util::getPermissions('metadata');
        $this->set('is_metadata', $bMetadata);
        if ($bMetadata)
        {
            $this->set('need_metadata', $this->Home->getMenuNeedsMetadata());
        }
    }

    function onAction_feedback()
    {
        $this->addCss('home/home.feedback');

        $this->addJqueryUi();
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('home/home.feedback');

        if (empty($_POST))
        {
            $user_id = Util::getUserId();
            if (!empty($user_id))
            {
                $user = new UserModel();
                $info = $user->getUser($user_id);

                $name = "{$info['firstname']} {$info['lastname']}";
                $this->set('name', $name);
                $this->set('email', $info['email']);
            }

            return;
        }

        $name = $_POST['name'];
        $email = $_POST['email'];
        $msg = $_POST['msg'];

        $this->set('name', $name);
        $this->set('email', $email);
        $this->set('msg', $msg);

        $msg = htmlspecialchars($msg);
        $msg = nl2br($msg);

        $params = array(
            'name' => $name,
            'email' => $email,
            'msg' => $msg,
        );

        $mail = new MailModel();
        $message = $mail->grab_data('home', 'email_feedback', $params);
        if (empty($message))
        {
            Util::logit('Failed to grab feedback', __FILE__, __LINE__);
            $this->set('err', 'Failed to format feedback');
            return;
        }

        $to = 'support+feedback@splatnom.com';
        $subject = 'Feedback!!';
        $bSent = $mail->send_smtp(null, $to, $subject, $message);
        if ($bSent !== true)
        {
            $err = 'Failed to send email feedback';
            Util::logit($err, __FILE__, __LINE__);
            $this->set('err', $err);
            return;
        }

        $this->set('feedback_done', true);
    }
}
