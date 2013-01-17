<?php

require_once FB_SDK;

class ShareController
    extends Controller
{

    function onAction_facebook($menu_id=null,$section_id=null, $item_id=null)
    {
        $is_json = isset($_GET['json']);

        $out = $this->go_facebook($menu_id, $section_id, $item_id);

        Util::logit($out);

        if (!empty($out['fb_error']['description']))
        {
            // if error, just go back to menu
            $this->redirect($out['menu_link']);
            return;
        }

        $this->set('is_json', $is_json);

        if (!$is_json)
        {
            $this->addCss('share/share.facebook');

            $this->set('status',        $out['status']);
            $this->set('from_url',      $out['menu_link']);
            $this->set('login_url',     $out['fb_login_url']);
            $this->set('fb_error',      $out['fb_error']['description']);
        }
        else
        {
            $this->m_bRender = false;
            $this->set('json_data', $out);
        }
    }

    function go_facebook($menu_id=null,$section_id=null, $item_id=null)
    {
        $fb_out = array(
            'status' => false,
            'menu_link' => '',
            'fb_login_url' => '',
            'redirect' => '',
            'fb_error' => array(
                'error' => '',
                'reason' => '',
                'description' => '',
            ),
        );

        if (!Util::getUserId())
        {
            $fb_out['redirect'] = '/home/main';
            Util::logit('[Share::Facebook] Must login to share on facebook');
            return $fb_out;
        }

        if (!empty($_GET))
        {
            // fb will have errors in $_GET
            // error_reason=user_denied&error=access_denied&error_description=The+user+denied+your+request.
            $fb_err         = @$_GET['error'];
            $fb_err_reason  = @$_GET['error_reason'];
            $fb_err_desc    = @$_GET['error_description'];

            $fb_out['fb_error'] = array(
                'error'         => $fb_err,
                'reason'        => $fb_err_reason,
                'description'   => $fb_err_desc,
            );
        }

        $menu = new MenuModel();
        $info = $menu->getIdAndNames($menu_id, $section_id, $item_id);

        if (empty($info))
        {
            $fb_out['redirect'] = '/home/main';
            Util::logit("[Share::Facebook] Menu '$menu_id' not available");
            return $fb_out;
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

        $fb_out['menu_link'] = $link;

        // finally... lets update facebook!
        $fb_config = array(
            'appId'     => FB_APP_ID,
            'secret'    => FB_APP_SECRET,
        );

        $facebook = new Facebook($fb_config);
        $user_id = $facebook->getUser();

        //Util::logit('[Share::Facebook] user id:'. $user_id);

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

                $fb_out['status'] = true;

                return $fb_out;
            } catch (FacebookApiException $e) {
                $user_id = false;
                $type = $e->getType();
                $msg  = $e->getMessage();

                Util::logit("[Share::Facebook] Type:$type Message:$msg");

                // do we need this? The next if stmt should tell the user to login
                $fb_out['fb_error'] = array(
                    'error'         => $type,
                    'reason'        => $type,
                    'description'   => $msg,
                );
            }
        }

        // if user was not logged in, or it was, but then it wasn't, we must login
        if (!$user_id)
        {
            $params = array(
                'scope' => 'publish_stream',
            );

            $login_url = $facebook->getLoginUrl($params);
            $fb_out['fb_login_url'] = $login_url;
        }

        return $fb_out;
    }
}
