<?php

class LoginController
    extends Controller
{
    function onAction_main()
    {
        global $get_url;

        $this->addCss('login');
        $this->addJs('login');
        
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        
        $this->set('username', '');
        $this->set('msg', '');
        
        $goto_url = '';
        if (!empty($_GET['goto']))
            $goto_url = $_GET['goto'];
        $this->set('goto_url', 'goto='.$goto_url);

        if (!empty($_POST))
        {
            $sess_id = $this->Login->tryLogin($_POST['lu'], $_POST['lp']);
            
            if ($sess_id !== false)
            {
                $_SESSION['id'] = $sess_id;
                
                $this->m_bRender = false;
                
                if (($goto_url == $get_url) || empty($goto_url))
                    redirect('/home/main');
                else 
                    redirect('/'.$goto_url);
            }
            else
            {
                $this->set('username', $_POST['lu']);
                $this->set('msg', 'Login failed!');
            }
        }
    }
    
    function onAction_end()
    {
        $this->m_bRender = false;
        session_destroy();
        redirect('/home/main');
    }
}
