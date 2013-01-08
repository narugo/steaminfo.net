<?php

class Stats extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->required_css = array(CSS_BOOTSTRAP, CSS_MAIN);
    }

    public function index()
    {
        $this->view->render("stats/index", 'Stats', $this->required_js, $this->required_css);
    }

}