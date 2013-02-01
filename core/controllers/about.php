<?php

class About extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->renderPage('about/index', 'About', array(JS_BOOTSTRAP), array(CSS_BOOTSTRAP, CSS_MAIN));
    }

}
