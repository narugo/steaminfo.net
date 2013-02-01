<?php

class Stats extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->renderPage('stats/index', 'Stats', array(), array(CSS_BOOTSTRAP, CSS_MAIN));
    }

}