<?php

class Index extends Controller {

    function __construct() {
        parent::__construct();

        $this->required_js = array('jquery', 'bootstrap', 'spin.min');
        $this->required_css = array('bootstrap.min', 'screen', 'index');
    }

    public function index() {
        $this->view->render("index", 'Steam Info', $this->required_js, $this->required_css);
    }

}
