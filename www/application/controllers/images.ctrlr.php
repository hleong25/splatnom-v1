<?php

class ImagesController
    extends Controller
{
    function __construct ($base_name, $action)
    {
        parent::__construct($base_name, $action);

        $this->m_bRender = false;
    }

    function onAction_pending($img_id=null)
    {
        $img_file = $this->Images->getPendingImage($img_id);
        $this->set('img_file', $img_file);
    }

    function onAction_pending_sm($img_id=null)
    {
        $this->onAction_pending($img_id);
        $this->set('thumbnail', true);
    }

    function onAction_menu($menu_id=null, $img_id=null)
    {
        $img_file = $this->Images->getMenuImage($menu_id, $img_id);
        $this->set('img_file', $img_file);
    }

    function onAction_menu_sm($menu_id=null, $img_id=null)
    {
        $this->onAction_menu($menu_id, $img_id);
        $this->set('thumbnail', true);
    }

    function onAction_fork()
    {
        $img_file = OS_IMAGE_PATH . DS . 'cutlery-fork.png';
        $this->set('img_file', $img_file);
    }
}
