<?php

class HomeController
    extends Controller
{
    function onAction_main()
    {
        $this->addCss('home');
        $this->addJs('home');
        
        $this->addJs('jquery.cookie', WEB_PATH_OTHER);
        $this->addJs('jquery.form', WEB_PATH_OTHER);
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        
        $this->set('recently_added', $this->Home->getNewlyAddedMenus());
    }
}
