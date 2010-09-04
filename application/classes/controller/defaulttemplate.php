<?php
 defined('SYSPATH') or die('No direct script access.');

 class Controller_DefaultTemplate extends Controller_Template
  {
     public    $template = 'layout/default';
     
     public function view ($template) {
     	$this->template = $template;
     	echo "view";
     }

     /**
      * Initialize properties before running the controller methods (actions),
      * so they are available to our action.
      */
     public function before()
      {
         // Run anything that need ot run before this.
         parent::before();
         
		echo "before";
/*         if($this->auto_render)
          {
            // Initialize empty values
            $this->template->title            = '';
            $this->template->meta_keywords    = '';
            $this->template->meta_description = '';
            $this->template->meta_copywrite   = '';
            $this->template->header           = '';
            $this->template->content          = '';
            $this->template->footer           = '';
            $this->template->styles           = array();
            $this->template->scripts          = array();
          }
          
         // Install Acl Liste
         $this->a2 = a2::instance('page-acl'); 
         $this->a1 = $this->a2->a1;
         $this->_name = Request::instance()->controller;
         $this->_action = Request::instance()->action;
         $this->user = $this->a2->get_user();
         //var_dump($this->_name,$this->_action,$this->a2);
         if(!$this->a2->allowed($this->_name,$this->_action))
         {            
           $this->request->redirect('denied');
           //die('Zugriff auf die Seite verweigert!');
         }*/
      }

     /**
      * Fill in default values for our properties before rendering the output.
      */
     public function after()
      {
      	echo "after";
         if($this->auto_render)
          {
             // Define defaults             
             $styles                  = array('application/views/layout/css/style.css' => 'screen');
             #$scripts                = array('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js');
             $scripts                 = array('application/views/layout/js/websim.js',
                                              'application/views/layout/js/jquery-1.4.2.js',                                                                  'application/views/layout/js/script.js'
                                              );

             // Add defaults to template variables.
             $this->template->styles  = array_reverse(array_merge($this->template->styles, $styles));
             $this->template->scripts = array_reverse(array_merge($this->template->scripts, $scripts));
             $this->user_info();
           }

         // Run anything that needs to run after this.
         parent::after();
      }
 }
