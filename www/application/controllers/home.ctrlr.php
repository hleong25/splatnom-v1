<?php

class HomeController
    extends Controller
{
    function onAction_main()
    {
        $this->addCss('home/home');
        $this->addJs('home/home');
        
        $this->addJs('jquery.cookie', WEB_PATH_OTHER);
        $this->addJs('jquery.form', WEB_PATH_OTHER);
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        
        $this->set('recently_added', $this->Home->getNewlyAddedMenus());

        if (getPermissions('metadata'))
        {
            $this->set('need_metadata', $this->Home->getMenuNeedsMetadata());
        }
    }
}
