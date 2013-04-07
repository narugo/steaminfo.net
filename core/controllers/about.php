<?php

class About extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        $this->view->renderPage('about/index', 'About', array(), array());
    }

}
