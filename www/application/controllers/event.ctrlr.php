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

                $saved_event_imgs = $this->Event->add_images($new_event_id, $files);
            }

            $this->redirect("/event/edit/$new_event_id");
        }
    }

    function onAction_edit($event_id=null)
    {
        if (!Util::getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('event/event.edit');
    }
}
