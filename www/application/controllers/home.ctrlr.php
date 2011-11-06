<?php

class HomeController
    extends Controller
{
    function onAction_main()
    {
        $this->addCss('table');
        $this->addCss('home/home');

        $this->addJs('jquery.cookie', WEB_PATH_OTHER);
        $this->addJs('jquery.form', WEB_PATH_OTHER);
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('home/home');

        $this->set('ready_menus', $this->Home->getReadyMenus());

        $bMetadata = Util::getPermissions('metadata');
        $this->set('is_metadata', $bMetadata);
        if ($bMetadata)
        {
            $this->set('need_metadata', $this->Home->getMenuNeedsMetadata());
        }
    }
}
