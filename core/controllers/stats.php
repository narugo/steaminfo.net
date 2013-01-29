<?php

class Stats extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->required_css = array(CSS_BOOTSTRAP, CSS_MAIN);
        $this->view->renderPage("stats/index", 'Steam Info - Stats', array(), $this->required_css);
    }

}