<?php

class Control extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        session_start();
        if (empty($_SESSION['id'])) {
            $this->view->renderPage("control/no_login", 'Control panel',
                array(JS_JQUERY, JS_BOOTSTRAP),
                array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN));
        } else {
            if ($_SESSION['id'] !== 'http://steamcommunity.com/openid/id/76561197994938134') {
                $this->view->renderPage("control/not_allowed", 'Control panel',
                    array(JS_JQUERY, JS_BOOTSTRAP),
                    array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN));
            } else {
                $this->view->renderPage("control/index", 'Control panel',
                    array(JS_JQUERY, JS_BOOTSTRAP),
                    array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN));
            }
        }
    }

}