<?php

class Stats extends Controller {

    function __construct() {
        parent::__construct();

        $this->required_js = array();
        $this->required_css = array('bootstrap.min', 'screen');
    }

    public function index() {
        $this->view->render("stats/index", 'Stats - Steam Info', $this->required_js, $this->required_css);
    }

}