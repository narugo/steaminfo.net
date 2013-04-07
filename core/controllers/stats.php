<?php

class Stats extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        $this->view->renderPage('stats/index', 'Stats', array(), array());
    }

}