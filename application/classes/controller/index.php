<?php
/**
 * @name index.php
 * 
 * Der Index Controller der Startseite.
 * 
 * @author Splasch
 * @version 0.1
 * @copyright Copyright (c) 2010, Splasch
 */
defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_DefaultTemplate
 {
    public function action_index()
     {        
        $content                 = array();
        $this->template->title   = 'WebSim das etwas andere Browsergame';

        #View::set_global('x', 'Diese ist eine Globale variable');        
        #$content['content']     = 'Hier entsteht ein neues Browsergame';
        
        $this->template->content = View::factory('pages/index');         
     }
        
    /**
     * @name action_another
     * 
     * Beschreibung
     * 
     * @return void      
     * @author splasch
     */    
    public function action_another()
     {
        $this->request->response = 'Andere action';
     }
    
    public function action_dynamic($say)
     {
        $this->request->response = 'Du sagst: '.$say;
     }
}