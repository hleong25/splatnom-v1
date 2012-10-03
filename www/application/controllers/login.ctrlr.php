<?php

class LoginController
    extends Controller
{
    function onAction_main()
    {
        global $get_url;

        $this->addCss('login/login.main');

        $this->addJqueryUi();
        $this->addJs('new.jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('login/login');

        $goto_url = '';
        if (!empty($_GET['goto']))
            $goto_url = $_GET['goto'];

        if (!empty($goto_url))
            $this->set('goto_url', 'goto='.$goto_url);

        if (!empty($_POST))
        {
            $sess_id = $this->Login->tryLogin($_POST['lu'], $_POST['lp']);

            if ($sess_id !== false)
            {
                $_SESSION['id'] = $sess_id;

                if (($goto_url == $get_url) || empty($goto_url))
                    $this->redirect('/home/main');
                else
                    $this->redirect('/'.$goto_url);

                return;
            }
            else
            {
                $this->set('username', $_POST['lu']);
                $this->set('msg', 'Login failed!');
            }
        }
    }

    function onAction_end()
    {
        session_destroy();
        $this->redirect('/home/main');
    }

    function onAction_forgot()
    {
        $user_id = Util::getUserId();
        if (!empty($user_id))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('login/login.forgot');

        $this->addJqueryUi();
        $this->addJs('new.jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('login/login.forgot');

        if (empty($_POST))
            return;

        if (empty($_POST['username']))
        {
            $this->set('err_msg', 'Userame is empty');
            return;
        }

        // if it's here, then let's send the request to reset password

        // always set it to true so it can't do a brute force hack on users
        $this->set('reset_sent', true);
        $username = $_POST['username'];

        $code = Util::getRandomString();

        $user = new UserModel();
        $user_info = $user->getUserByUsername($username);

        if (empty($user_info))
        {
            Util::logit("Failed to send forgot request because user '{$username}' does not exists.", __FILE__, __LINE__);
            return;
        }

        $reset = $this->Login->set_reset_code($username, $code);
        if (empty($reset))
        {
            Util::logit("Failed to send forgot request for user '{$username}' because it can't set the code.", __FILE__, __LINE__);
            return;
        }

        $sent = $this->send_forgot_code($user_info, $code);
        if (empty($sent))
        {
            Util::logit("Failed to send forgot request for user '{$username}' because it can't send the email.", __FILE__, __LINE__);
            return;
        }

        Util::logit("Reset password request: user({$username}) code({$code})", __FILE__, __LINE__);
    }

    function send_forgot_code($user_info, $code)
    {
        $reset_url = Util::getDomain().'/login/reset';
        $code_url = "$reset_url/{$code}";

        $user = $user_info['username'];
        $email = $user_info['email'];

        $mail = new MailModel();
        $subject = 'splatnom: Reset password request for '.$user;

        $params = array(
            'user' => $user,
            'code' => $code,
            'reset_url' => $reset_url,
            'code_url' => $code_url,
        );

        $message = $mail->grab_data('login', 'email_reset', $params);
        if (empty($message))
        {
            Util::logit('Failed to send reset password request', __FILE__, __LINE__);
            return false;
        }

        $sent = $mail->queue(null, $email, $subject, $message);

        return $sent;
    }

    function onAction_reset($code=null)
    {
        $user_id = Util::getUserId();
        if (!empty($user_id))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('login/login.reset');

        $this->addJqueryUi();
        $this->addJs('new.jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('login/login.reset');

        $username = '';
        $password1 = '';
        $password2 = '';

        if (!empty($_POST))
        {
            $username = $_POST['username'];
            $code = $_POST['reset_code'];
            $password1 = $_POST['password'];
            $password2 = $_POST['password2'];
        }

        $this->set('username', $username);
        $this->set('reset_code', $code);

        if (empty($username))
            return;

        $user = new UserModel();
        $user_info = $user->getUserByUsername($username);

        if (empty($user_info))
        {
            Util::logit("Failed to reset user: no user found for '{$username}'", __FILE__, __LINE__);
            $this->set('err', 'Failed to reset user');
            return;
        }

        if (empty($password1) || empty($password2))
        {
            $this->set('err', 'Invalid password');
            return;
        }
        else if ($password1 !== $password2)
        {
            $this->set('err', 'Passwords does not match');
            return;
        }
        else if (strlen($password1) < 4)
        {
            $this->set('err', 'Password too short');
            return;
        }

        $user_id = $user_info['id'];
        $verify = $this->Login->verify_reset_code($user_id, $code);
        if (empty($verify))
        {
            Util::logit("Failed to reset user: user({$username}) id({$user_id}) code({$code})", __FILE__, __LINE__);
            $this->set('err', 'Failed to reset user');
            return;
        }

        // finally, if it's here, then it's good to reset password
        $reset = $user->setUserPassword($user_id, $password1);
        if (empty($reset))
        {
            Util::logit("Failed to reset user: password bad for user '{$username}'", __FILE__, __LINE__);
            $this->set('err', 'Failed to reset user');
            return;
        }

        $this->send_new_password($user_info, $password1);
        $this->set('is_reset', true);
    }

    function send_new_password($user_info, $new_password)
    {
        $user = $user_info['username'];
        $email = $user_info['email'];

        $mail = new MailModel();
        $subject = 'splatnom: Password changed for '.$user;

        $params = array(
            'user' => $user,
            'password' => $new_password,
        );

        $message = $mail->grab_data('login', 'email_new_password', $params);
        if (empty($message))
        {
            Util::logit('Failed to send new password update', __FILE__, __LINE__);
            return false;
        }

        $sent = $mail->queue(null, $email, $subject, $message);

        return $sent;
    }
}
