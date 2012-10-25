<?php

class Stats extends Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->view->render("stats/index");
    }

}
