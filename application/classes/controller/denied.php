<?php
/**
 * @name denied.php
 * 
 * Der Denied Controller der Startseite.
 * 
 * @author Splasch
 * @version 0.1
 * @copyright Copyright (c) 2010, Splasch
 */
defined('SYSPATH') or die('No direct script access.');

class Controller_Denied extends Controller_DefaultTemplate
 {
    public function action_index()
     {        
        $this->template->title   = 'Access Denied';       
        $this->template->content = View::factory('pages/denied');         
     }
        
 }     