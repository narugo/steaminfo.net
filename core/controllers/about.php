<?php

class About extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->required_js = array(JS_BOOTSTRAP);
        $this->required_css = array(CSS_BOOTSTRAP, CSS_MAIN);
    }

    public function index()
    {
        $this->view->render("about", 'About', $this->required_js, $this->required_css);
    }

}
