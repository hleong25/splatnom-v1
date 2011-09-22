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
        $this->addJs('user/user.register');

        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

        if (empty($_POST))
            return;

        $params = array(
            'fname'=>array('lbl'=>'First name', 'length'=>30),
            'lname'=>array('lbl'=>'Last name', 'length'=>30),
            'email'=>array('lbl'=>'Email', 'length'=>100),
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

            if (($key === 'password') || ($key === 'password2'))
                continue;

            $this->set($key, $_POST[$key]);
        }

        $firstname = $_POST['fname'];
        $lastname = $_POST['lname'];
        $email = $_POST['email'];

        $username = $_POST['username'];
        $password1 = $_POST['password'];
        $password2 = $_POST['password2'];

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
            'email' => $email,
            'username' => $username,
            'password' => $password1,
        );

        $new_id = $this->User->addNewUser($info);
        $this->set('user_add', $new_id);

        if ($new_id !== false)
        {
            $_SESSION['id'] = $new_id;
            $this->redirect('/home/main');
            return;
        }

        /*
            Setup first user to admin...

            insert ignore into tblUserPermissions(user_id, permission_id)
            values ((select id from tblUsers order by id asc limit 1),
            (select id from vPermissions where permission='admin'))
        */
    }

}
