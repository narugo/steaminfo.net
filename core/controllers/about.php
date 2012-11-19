<?php

class About extends Controller {

   function __construct() {
       parent::__construct();

       $this->required_js = array('bootstrap');
       $this->required_css = array('bootstrap.min', 'screen');
   }

    public function index() {
        $this->view->render("about", 'About - Steam Info', $this->required_js, $this->required_css);
    }

}
