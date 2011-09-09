<?php

class UserController
    extends Controller
{
    function onAction_register()
    {
        if (!empty($_SESSION['id']))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('user/user.register');
        $this->addJs('user/user.register');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

        $this->set('username', '');
        $this->set('err_msg', false);

        if (empty($_POST) || empty($_POST['username']))
        {
            return;
        }

        $username = $_POST['username'];
        $this->set('username', $username);

        if (!$this->User->isUsernameAvailable($username))
        {
            $this->set('err_msg', 'Username is unavailable');
            return;
        }

        if (strlen($username) > 25)
        {
            $this->set('err_msg', 'Username is too long');
            return;
        }

        if ( strlen($_POST['password']) == 0)
        {
            $this->set('err_msg', 'Password sucks!');
            return;
        }

        // if its here, then let's add the new user
        $info = array(
            'username' => $username,
            'password' => $_POST['password'],
        );

        $new_id = $this->User->addNewUser($info);
        $this->set('user_add', $new_id);

        if ($new_id !== false)
        {
            $_SESSION['id'] = $new_id;
            $this->redirect('/home/main');
            return;
        }
    }

}
