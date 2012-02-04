<?php

class UserController
    extends Controller
{
    function onAction_register()
    {
        // http://thinkdiff.net/mysql/encrypt-mysql-data-using-aes-techniques/

        if (!empty($_SESSION['id']))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('user/user.register');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('user/user.register');

        if (empty($_POST))
            return;

        $params = array(
            'fname'=>array('lbl'=>'First name', 'length'=>30),
            'lname'=>array('lbl'=>'Last name', 'length'=>30),
            'email'=>array('lbl'=>'Email', 'length'=>100),
            'email2'=>array('lbl'=>'Confirm email', 'length'=>100),
            'username'=>array('lbl'=>'Username', 'length'=>50),
            'password'=>array('lbl'=>'Password', 'length'=>100),
            'password2'=>array('lbl'=>'Password', 'length'=>100),
        );

        foreach ($params as $key=>$val)
        {
            if (empty($_POST[$key]))
            {
                $this->set('err_msg', "{$val['lbl']} is empty.");
                return;
            }

            if (strlen($_POST[$key]) > $val['length'])
            {
                $this->set('err_msg', "{$val['lbl']} is too long.");
                return;
            }

            // if it's password or password2 don't set the user page
            if (($key === 'password') || ($key === 'password2'))
                continue;

            $this->set($key, $_POST[$key]);
        }

        $firstname = $_POST['fname'];
        $lastname = $_POST['lname'];
        $email1 = $_POST['email'];
        $email2 = $_POST['email2'];

        $username = $_POST['username'];
        $password1 = $_POST['password'];
        $password2 = $_POST['password2'];

        if (empty($email1) || empty($email2) ||
            ($email1 !== $email2))
        {
            $this->set('err_msg', 'Email address does not match');
            return;
        }

        if (empty($password1) || empty($password2) ||
            ($password1 !== $password2))
        {
            $this->set('err_msg', 'Passwords does not match');
            return;
        }

        if (strlen($password1) < 4)
        {
            $this->set('err_msg', 'Passwords is too short.');
            return;
        }

        if (!$this->User->isUsernameAvailable($username))
        {
            $this->set('err_msg', 'Username is unavailable');
            return;
        }

        // finally!!  let's add this new user
        $info = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email1,
            'username' => $username,
            'password' => $password1,
        );

        $new_id = $this->User->addNewUser($info);
        $this->set('user_add', $new_id);

        $sVerifyCode = $this->User->setUserVerifyCode($new_id);
        if ($sVerifyCode !== false)
        {
            // the verifcation code worked, lets email it to the user...
            $bSent = $this->sendVerificationToUser($username, $email1, $sVerifyCode);
            if ($bSent !== true)
            {
                $this->set('err_msg', 'Sending verification to user failed');
                $new_id = false;
            }
        }

        if ($new_id !== false)
        {
            $_SESSION['id'] = $new_id;
            $this->redirect('/home/main');
            return;
        }
    }

    function sendVerificationToUser($user, $emailTo, $verifyCode)
    {
        $mail = new MailModel();
        $subject = 'Verify account for splatnom';

        $params = array(
            'user' => $user,
            'verifyCode' => $verifyCode,
        );

        $message = $mail->grab_data('user', 'email_verification', $params);
        if (empty($message))
        {
            Util::logit('Failed to send email verification', __FILE__, __LINE__);
            return false;
        }

        $sent = $mail->send_smtp(null, $emailTo, $subject, $message);

        return $sent;
    }

    function onAction_verify($code=null)
    {
        // TODO: add CSS for style and JS for focus on username textbox

        if (empty($code))
        {
            $this->redirect('/home/main');
            return;
        }

        //$this->addCss('user/user.verify');
        //$this->addJs('user/user.verify');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

        $this->set('code', $code);

        if (Util::getUserId() !== false)
        {
            // user must be logged off to verify account.
            global $get_url;
            session_destroy();
            $this->redirect($get_url);
            return;
        }

        if (empty($_POST))
            return;

        $username = $_POST['username'];
        $password = $_POST['password'];

        $this->set('username', $username);

        $login = new LoginModel();
        $user_id = $login->tryLogin($username, $password);

        $bVerify = $this->User->verifyUserCode($user_id, $code);
        if ($bVerify)
        {
            $_SESSION['id'] = $user_id;
            $this->redirect('/home/main');
            return;
        }
        else
        {
            $this->set('err_msg', 'Could not validate account');
            return;
        }
    }

}
