<?php

class Apps extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->renderPage('apps/index', 'Apps', array(), array(CSS_BOOTSTRAP, CSS_MAIN));
    }

}