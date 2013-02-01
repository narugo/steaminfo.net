<?php

class Control extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (empty($_SESSION['id'])) {
            header('Location: ' . WEBSITE_URL . 'auth/');
        } else {
            $this->view->renderPage('control/index', 'Control Panel',
                array(JS_JQUERY, JS_BOOTSTRAP),
                array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN));
        }
    }

}