<?php

require_once FB_SDK;

class ShareController
    extends Controller
{

    function onAction_facebook($menu_id=null,$section_id=null, $item_id=null)
    {
        if (!empty($_GET))
        {
            $is_json = isset($_GET['json']);

            // fb will have errors in $_GET
            // error_reason=user_denied&error=access_denied&error_description=The+user+denied+your+request.
            $fb_err         = @$_GET['error'];
            $fb_err_reason  = @$_GET['error_reason'];
            $fb_err_desc    = @$_GET['error_description'];
        }

        // set the default to not show header/footer
        $this->m_bRender = false;

        // set the default response as a failure
        $this->set('share_ok', false);

        if (empty($menu_id))
        {
            $this->redirect('/home/main');
            return;
        }

        $menu = new MenuModel();
        $info = $menu->getIdAndNames($menu_id, $section_id, $item_id);

        if (empty($info))
        {
            // weird... no info
            $this->redirect('/home/main');
            return;
        }

        // setup slug info
        $id   = $info['menu_id'];
        $slug = Util::slugify($info['menu']);
        $link = sprintf('http://%s/menu/view/%s', Util::getDomain(), "$id-$slug");
        $msg  = 'I stuck a fork in it!';
        $img  = sprintf('http://%s/%s', Util::getDomain(), 'img/logo.smartphone.jpg');

        if (!empty($info['section']))
        {
            $id   = $info['menu_id'];
            $slug = Util::slugify($info['menu']);
            $link = sprintf('http://%s/menu/images/%s', Util::getDomain(), "$id-$slug");

            $id    = $info['section_id'];
            $slug  = Util::slugify($info['section']);
            $link .= "/$id-$slug";
        }

        if (!empty($info['item_id']))
        {
            $id = $info['item_id'];
            $slug = Util::slugify($info['item']);
            $link .= "/$id-$slug";
            $msg  = "I stuck a fork in {$info['item']} at {$info['menu']}!";
        }

        // finally... lets update facebook!
        $fb_config = array(
            'appId'     => FB_APP_ID,
            'secret'    => FB_APP_SECRET,
            'cookie'    => true,
        );

        $facebook = new Facebook($fb_config);
        $user_id = $facebook->getUser();

        if ($user_id)
        {
            try {

                $params = array(
                    'link' => $link,
                    'message' => $msg,
                    'picture' => $img,
                );

                $ret_obj = $facebook->api(
                    '/me/feed',
                    'POST',
                    $params
                );

                $this->set('share_ok', true);

                return;
            } catch (FacebookApiException $e) {
                $user_id = false;
                $type = $e->getType();
                $msg  = $e->getMessage();
                Util::logit("[Share::Facebook] Type:$type Message:$msg");
            }
        }

        // if user was not logged in, or it was, but then it wasn't, we must login
        if (!$user_id)
        {
            $params = array(
                'scope' => 'publish_stream',
            );

            //$redirect_url = sprintf('http://%s%s', Util::getDomain(), $link);
            //$params['redirect_uri'] = $link;

            $login_url = $facebook->getLoginUrl($params);
            $this->set('share_ok', false);
            $this->set('login_url', $login_url);

        }
    }

}
