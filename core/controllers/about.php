<?php

class About extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->renderPage('about/index', 'About', array(), array());
    }

}
