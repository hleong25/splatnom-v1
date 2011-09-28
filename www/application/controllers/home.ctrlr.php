<?php

class HomeController
    extends Controller
{
    function onAction_main()
    {
        $this->addCss('home/home');
        $this->addJs('home/home');

        $this->addCss('table');

        $this->addJs('jquery.cookie', WEB_PATH_OTHER);
        $this->addJs('jquery.form', WEB_PATH_OTHER);
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);

        $this->set('ready_menus', $this->Home->getReadyMenus());

        $bMetadata = UtilsModel::getPermissions('metadata');
        $this->set('is_metadata', $bMetadata);
        if ($bMetadata)
        {
            $this->set('need_metadata', $this->Home->getMenuNeedsMetadata());
        }
    }
}
