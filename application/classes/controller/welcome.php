<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

    public function action_index()
    {
        $view = new View('template');
        $html = '<form action="" method="POST">';
        $files = Configuration::i18n('en');
        if(empty($_POST))
        {
            if(isset($_GET['f']) and isset($files[$_GET['f']]))
            {
                $html .= '<input type="hidden" name="file" value="'.$_GET['f'].'" />';
                $html .= Configuration::parse(Kohana::load($files[$_GET['f']]), 'i');
            }
            else
            {
                $html .= '<ul>';
                foreach($files as $key => $file)
                {
                    $html .= '<li><a href="/welcome/index?f='.$key.'">'.$file.'</a></li>';
                }
                $html .= '</ul>';
            }
        }
        else
        {
            $file = $_POST['file'];
            Configuration::save($files[$file], $_POST['i']);
            $html .= var_export($_POST['i'], TRUE);
        }
        $view->content = $html.'<input type="submit" /></form>';//new View('test');
        $this->request->response = $view->render();
    }

    public function action_config()
    {
        $view = new View('template');
        $html = '<form action="" method="post">';
        $files = Configuration::config();
        if(empty($_POST))
        {
            if(isset($_GET['f']) and isset($files[$_GET['f']]))
            {
                $html .= '<input type="hidden" name="file" value="'.$_GET['f'].'" />';
                $html .= Configuration::parse(Kohana::load($files[$_GET['f']]), 'i');
            }
            else
            {
                $html .= '<ul class="listf">';
                foreach($files as $key => $file)
                {
                    $html .= '<li><a href="/welcome/config?f='.$key.'">'.$file.'</a></li>';
                }
                $html .= '</ul>';
            }
        }
        else
        {
            $file = $_POST['file'];
            Configuration::save($files[$file], $_POST['i']);
            $html .= var_export($_POST['i'], TRUE);
        }
        $view->content = $html.'<input type="submit" /></form>';//new View('test');
        $this->request->response = $view->render();
        //~ $this->request->response = '<pre>'.print_r(Configuration::config(), TRUE).'</pre>';
    }

} // End Welcome
