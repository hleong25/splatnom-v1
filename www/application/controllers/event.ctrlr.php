<?php

class EventController
    extends Controller
{
    function onAction_new()
    {
        if (!Util::getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('event/event.new');

        if (!empty($_POST))
        {
            // since saving events will redirect to editting them, there's no way to see the error
            //if (Util::isUploadOk() == false)
            //{
            //    $uploadMaxSize = ini_get('post_max_size');
            //    $this->set('err_msg', "Uploaded files exceeded {$uploadMaxSize}!");
            //}

            $info = array(
                'name' => $_POST['event_name'],
                'notes' => $_POST['event_notes'],
            );

            $new_event_id = $this->Event->create_new($info);
            if (empty($new_event_id))
            {
                $this->set('err_msg', 'Failed to save event!');
                return;
            }

            if (Util::isUploadOk() == true)
            {
                // add the cover image if needed
                $event_path = OS_EVENT_PATH . DS . $new_event_id;
                $files = Util::handle_upload_files($event_path);

                if (!empty($files))
                {
                    $saved_event_imgs = $this->Event->add_images($new_event_id, $files);
                }
            }

            $this->redirect("/event/edit/$new_event_id");
        }
    }

    function onAction_edit($event_id=null)
    {
        $user_id = Util::getUserId();
        $bPermMdt = Util::getPermissions('admin');
        if (empty($event_id) || ($event_id < 0) || empty($user_id) || empty($bPermMdt))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('event/edit');
        $this->addCss('event/view'); // to force update of /css/event/view.css

        $this->addJqueryUi();
        $this->addJs('jquery.tmpl.min', WEB_PATH_OTHER);
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('tinymce/tinymce-4.0b2-jquery/tinymce.min', WEB_PATH_OTHER);
        $this->addJs('event/edit');

        $this->set('event_id', $event_id);
        $this->set('google_api_key', GOOGLE_API_KEY);

        $load_from_db = empty($_POST);

        if (!$load_from_db)
        {
            $load_from_db = !$this->edit_onPost($event_id);
        }

        if ($load_from_db)
        {
            $this->get_event_details($event_id);
        }
    }

    function edit_onPost($event_id)
    {
        if (empty($_POST)) return false;

        //Util::logit($_POST);

        $event = $this->Event;

        $info = array(
            'name' => $_POST['info_name'],
            'notes' => $_POST['info_notes'],
            'address' => $_POST['info_address'],
            'latitude' => $_POST['info_latitude'],
            'longitude' => $_POST['info_longitude'],
            'dates' => $_POST['info_dates'],
            'cover_img' => array(
                'file_img' => $_POST['info_cover_img'],
            ),
        );

        $update_info_ok = $event->update_event_info($event_id, $info);
        if (empty($update_info_ok)) return false;
        // TODO: what to do if fail when update

        $this->set('info', $info);

        $post_vendors = $_POST['vendor'];

        $vendors = array();
        $ordinal_vendor = 0;

        for ($ii = 0, $jj = count($post_vendors); $ii < $jj; $ii++)
        {
            switch ($post_vendors[$ii])
            {
                case '@vendor@':
                    $info = array(
                        'vendor_id' => (int)$post_vendors[++$ii],
                        'ordinal' => $ordinal_vendor++,
                        'name' => trim($post_vendors[++$ii]),
                        'description' => trim($post_vendors[++$ii]),
                    );
                    $vendors[] = $info;

                    break;
            }
        }

        $update_vendors_ok = $event->update_vendors($event_id, $vendors);
        if (empty($update_vendors_ok))
        {
            $this->set('err_msg', 'Failed to save vendor information');
        }

        $this->set('vendors', $vendors);

        return true;
    }

    function get_event_details($event_id)
    {
        $event = $this->Event;

        $event_info = $event->get_event($event_id);

        if (empty($event_info))
        {
            return false;
        }

        $this->set('info', $event_info);

        $vendors = $event->get_vendors($event_id);
        $this->set('vendors', $vendors);

        return true;
    }

    function onAction_view($event_id)
    {
        if (empty($event_id) || ($event_id < 0))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('event/view');

        $this->set('event_id', $event_id);

        $get_event_ok = $this->get_event_details($event_id);
        if (empty($get_event_ok))
        {
            $this->redirect('/home/main');
        }
    }
}
