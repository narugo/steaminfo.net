<?php

class About extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->required_js = array(JS_BOOTSTRAP);
        $this->required_css = array(CSS_BOOTSTRAP, CSS_MAIN);
        $this->view->renderPage("about", 'Steam Info - About', $this->required_js, $this->required_css);
    }

}
