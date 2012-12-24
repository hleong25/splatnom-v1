<?php

require_once(RECAPTCHA_LIB);

class HomeController
    extends Controller
{
    function onAction_main()
    {
        $this->addJqueryUi();
        $this->addCss('home/home.main');

        $this->addJs('new.jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('home/home');

        $loc = Util::cookie('location');
        if (!empty($loc))
        {
            $this->set('location', $loc);
        }

        $this->set('ready_menus', $this->Home->getReadyMenus(12));

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
        $this->addJs('new.jquery.watermark.min', WEB_PATH_OTHER);
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

        $recaptcha_resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY,
            $_SERVER["REMOTE_ADDR"],
            $_POST["recaptcha_challenge_field"],
            $_POST["recaptcha_response_field"]);

        if (!$recaptcha_resp->is_valid)
        {
            $this->set('err', 'Failed to verify reCAPTCHA.');
            return;
        }

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
        $bSent = $mail->queue(null, $to, $subject, $message);
        if (empty($bSent))
        {
            $err = 'Failed to send email feedback';
            Util::logit($err, __FILE__, __LINE__);
            $this->set('err', $err);
            return;
        }

        $this->set('feedback_done', true);
    }

    function onAction_gmapmenu()
    {
        $this->addCss('home/home.gmapmenu');
        $this->addJs('jquery.tmpl.min', WEB_PATH_OTHER);
        $this->addJs('home/home.gmapmenu');

        // http://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=SET_TO_TRUE_OR_FALSE
        $gmap_key = GOOGLE_API_KEY;
        $gmap_url = "http://maps.googleapis.com/maps/api/js?key={$gmap_key}&sensor=false";
        $this->addRemoteJs($gmap_url);

        $menus = $this->Home->getReadyMenus();
        $menus_tmpl_data = array();

        foreach ($menus as &$menu)
        {
            $menu_id = $menu['id'];
            $name = $menu['name'];
            $addy = $menu['address'];
            $slugify = Util::slugify($menu['name']);

            $link = '/menu/view/%d-%s';
            $menu_link = sprintf($link, $menu_id, $slugify);

            $lat = $menu['latitude'];
            $lng = $menu['longitude'];

            $menus_tmpl_data["$lat:$lng"][] = array(
                'lat'  => $lat,
                'lng'  => $lng,
                'link' => $menu_link,
                'name' => $name,
                'addy' => $addy
            );
        }

        $this->set('menus_tmpl_data', $menus_tmpl_data);
    }

    function onAction_about()
    {
        $this->addCss('home/home.about');

        $this->addJs('tinymce/tinymce-3.5b3/tiny_mce', WEB_PATH_OTHER);
        $this->addJs('home/home.about');

        if (!empty($_POST))
        {
            $editor_src = $_POST['editor'];
            $this->set('editor_src', $editor_src);
        }
    }
}
