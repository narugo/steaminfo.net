<?php

class Apps extends Controller {

   function __construct() {
       parent::__construct();
   }

    public function index() {
        $this->view->render("apps");
    }

}
