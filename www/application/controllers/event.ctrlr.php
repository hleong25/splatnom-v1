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

        $this->addCss('event/new');

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

        $event_info = $this->Event->get_event($event_id);
        if (empty($event_info))
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
        $this->set('is_admin', Util::getPermissions('admin'));
        $this->set('is_metadata', Util::getPermissions('metadata'));
        $this->set('google_api_key', GOOGLE_API_KEY);

        $load_from_db = empty($_POST);

        if (!$load_from_db)
        {
            $load_from_db = !$this->edit_onPost($event_id, $event_info);
        }

        if ($load_from_db)
        {
            $this->get_event_details($event_id, $event_info);
        }
    }

    function edit_onPost($event_id, $event_info)
    {
        if (empty($_POST)) return false;

        //Util::logit($_POST);

        $event = $this->Event;

        // update status
        $status = $_POST['info_status'];
        if (!$event->update_event($event_id, $status))
            $err_msgs[] = 'Failed to update event status.';

        // get event dates
        $info_dates = array();
        foreach ($_POST['info_dates'] as $info_date)
        {
            $info_date = trim($info_date);

            $date = DateTime::createFromFormat('Y-m-d', $info_date);
            if (empty($date))
                continue;

            $dt_start = $date->format('Y-m-d').' 00:00:00';
            $dt_end = $date->format('Y-m-d').' 23:59:59';

            $info_dates[] = array(
                'label' => $info_date,
                'start' => $dt_start,
                'end' => $dt_end,
            );
        }

        // update event info
        $info = array(
            'name' => $_POST['info_name'],
            'notes' => $_POST['info_notes'],
            'address' => $_POST['info_address'],
            'latitude' => $_POST['info_latitude'],
            'longitude' => $_POST['info_longitude'],
            'dates'=> $info_dates,
            'cover_img' => array(
                'file_img' => $_POST['info_cover_img'],
            ),
        );

        $update_info_ok = $event->update_event_info($event_id, $info);
        if (empty($update_info_ok)) return false;
        // TODO: what to do if fail when update

        // reuse event_info['status'] and set the value
        foreach ($event_info['status'] as $ei_status)
        {
            $info['status'][] = array(
                'status'=>$ei_status['status'],
                'selected'=> ($ei_status['status'] == $status) ? 1 : 0,
            );
        }

        // set the info so the renderer can use it
        $this->set('info', $info);

        // update vendor data
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
                        'section_group' => trim($post_vendors[++$ii]),
                        'section' => trim($post_vendors[++$ii]),
                        'description' => trim($post_vendors[++$ii]),
                        'is_detailed' => false,
                    );

                    $vendors[] = $info;
                    break;

                case '@vendor_attr@':
                    $info = array_pop($vendors);

                    $attributes = array(
                        'is_detailed',
                    );

                    ++$ii; // point to the next item so we can get the attribute name

                    foreach ($attributes as $attr)
                    {
                        if (($post_vendors[$ii] === $attr) && ($post_vendors[$ii + 1] === 'on'))
                        {
                            ++$ii;
                            $info[$attr] = true;
                            break;
                        }
                    }

                    $vendors[] = $info;
                    break;

                case '@end_of_vendor@':
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

    function get_event_details($event_id, $event_info)
    {
        if (empty($event_info))
        {
            return false;
        }

        $event = $this->Event;

        $this->set('info', $event_info);

        $vendors = $event->get_vendors($event_id);
        $this->set('vendors', $vendors);

        $taggits = $event->get_event_image_taggits($event_id);

        // link the vendor to the tagged images
        foreach ($vendors as &$vendor)
        {
            $vendor_id = $vendor['vendor_id'];

            $vendor['taggits'] = array();

            foreach ($taggits as $taggit)
            {
                $file_img = $taggit['file_img'];
                $tagged_vendors = $taggit['vendor_id'];

                foreach ($tagged_vendors as $tagged)
                {
                    if ($vendor_id == $tagged)
                    {
                        $vendor['taggits'][] = $file_img;
                    }
                }
            }
        }

        // reset the vendors so the changes stick
        $this->set('vendors', $vendors);

        $name = $event_info['name'];
        $this->set('meta_title', $name);
        $this->set('meta_desc', "Delicious food at {$name}... mmMmmmMmmm nom nom nom says Zoidberg! (\/)(',,,,')(\/)");

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

        $event_info = $this->Event->get_event($event_id);
        if (empty($event_info))
        {
            $this->redirect('/home/main');
            return;
        }

        $get_event_ok = $this->get_event_details($event_id, $event_info);
        if (empty($get_event_ok))
        {
            $this->redirect('/home/main');
        }
    }

    function onAction_upcoming()
    {
        $this->addCss('event/upcoming');

        $list = $this->Event->get_upcoming();

        $this->set('upcoming_list', $list);
    }

    function onAction_all()
    {
        $this->addCss('event/all');

        $list = $this->Event->get_all();

        $this->set('list', $list);
    }

    function onAction_taggit($type=null, $event_id=null, $vendor_id=null)
    {
        if (empty($event_id) || ($event_id < 0))
        {
            $this->redirect('/home/main');
            return;
        }

        if (empty($vendor_id) || ($vendor_id < 0))
        {
            $this->redirect('/home/main');
            return;
        }

        $event = $this->Event;

        if (!empty($_POST))
        {
            // save it!

            $save_data = array(
                'taggits' => array(),
            );

            $taggits = array();
            if (!empty($_POST['taggits']))
                $taggits = $_POST['taggits'];

            foreach ($taggits as $img_id => $checked)
            {
                $save_data['taggits'][] = $img_id;
            }

            $taggits_ok = $event->apply_taggits($event_id, $vendor_id, $save_data['taggits']);
            if (!$taggits_ok)
            {
                // TODO: show the error
                $this->set('err_msg', 'Failed to save taggits');
            }
        }

        $this->addCss('event/taggit');

        $this->set('event_id', $event_id);
        $this->set('vendor_id', $vendor_id);

        $event_info = $event->get_event($event_id);
        if (empty($event_info))
        {
            $this->redirect('/home/main');
            return;
        }

        $get_event_ok = $this->get_event_details($event_id, $event_info);
        if (empty($get_event_ok))
        {
            $this->redirect('/home/main');
            return;
        }

        $vendors = $event->get_vendors($event_id, $vendor_id);
        if (empty($vendors) || empty($vendors[$vendor_id]))
        {
            $this->redirect('/home/main');
            return;
        }

        $vendor = $vendors[$vendor_id];
        $this->set('vendor_info', $vendor);

        $taggits = $event->get_event_image_taggits($event_id, $vendor_id);
        $this->set('taggits', $taggits);
    }

    function onAction_update_cover($event_id=null)
    {
        if (empty($event_id) || ($event_id < 0))
        {
            $this->redirect('/home/main');
            return;
        }

        $event = $this->Event;

        if (!empty($_POST))
        {
            // save it!

            $save_data = array(
                'cover_img_id' => -1,
            );

            if (!empty($_POST['cover_img_id']))
                $save_data['cover_img_id'] = $_POST['cover_img_id'];

            if ($save_data['cover_img_id'] > 0)
            {
                $cover_img_ok = $event->update_cover_img($event_id, $save_data['cover_img_id']);
                if (!$cover_img_ok)
                {
                    // TODO: show the error
                    $this->set('err_msg', 'Failed to save cover image');
                }
            }
        }

        $this->addCss('event/update_cover');

        $this->set('event_id', $event_id);

        $event_info = $event->get_event($event_id);
        if (empty($event_info))
        {
            $this->redirect('/home/main');
            return;
        }

        $get_event_ok = $this->get_event_details($event_id, $event_info);
        if (empty($get_event_ok))
        {
            $this->redirect('/home/main');
            return;
        }

        $imgs = $event->get_event_image_taggits($event_id, /*no vendor = all images*/ '');
        $this->set('imgs', $imgs);
    }
}
