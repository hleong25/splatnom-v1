<?php

class LoginController
    extends Controller
{
    function onAction_main()
    {
//        $return_url = func_get_args();
//        $url = implode('/', $return_url);
//        $this->set('url', $url);
        
        $this->addCss('login');
        $this->addJs('login');
        
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        
        $this->set('username', '');
        $this->set('msg', '');
        
        if (!empty($_POST))
        {
            $sess_id = $this->Login->tryLogin($_POST['lu'], $_POST['lp']);
            
            if ($sess_id !== false)
            {
                $_SESSION['id'] = $sess_id;
                
                $this->m_bRender = false;
                
//                if (empty($_POST['from_url']))
                    redirect('/home/main');
//                else
//                    redirect('/'.$_POST['from_url']);
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
