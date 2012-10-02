<?php

class Groups extends Controller {

   function __construct() {
       parent::__construct();
   }

    public function index() {
        $this->view->render("groups");
    }

    public function search() {
        // TODO: Implemet search
    }

}
