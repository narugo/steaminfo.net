<?php

class Control extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        if (empty($_SESSION['id'])) {
            header('Location: ' . WEBSITE_URL . 'auth/');
        } else {
            $this->view->renderPage('control/index', 'Control Panel', array(), array(CSS_FONT_AWESOME));
        }
    }

}