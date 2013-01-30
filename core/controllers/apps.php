<?php

class Apps extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->required_css = array(CSS_BOOTSTRAP, CSS_MAIN);
        $this->view->renderPage("apps/index", 'Steam Info - Apps', array(), $this->required_css);
    }

}