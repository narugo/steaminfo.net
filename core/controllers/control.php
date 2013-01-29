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
            $this->view->renderPage("control/no_login", 'Control panel',
                array(JS_JQUERY, JS_BOOTSTRAP),
                array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN));
        } else {
            if (self::isUserAllowed()) {
                $this->view->renderPage("control/index", 'Control panel',
                    array(JS_JQUERY, JS_BOOTSTRAP),
                    array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN));
            } else {
                $this->view->renderPage("control/not_allowed", 'Control panel',
                    array(JS_JQUERY, JS_BOOTSTRAP),
                    array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN));
            }
        }
    }

    private function isUserAllowed()
    {
        return TRUE;

        $whitelist = array(
            '76561197994938134'
        );
        foreach ($whitelist as $id) {
            if ($id === str_replace('http://steamcommunity.com/openid/id/', '', $_SESSION['id'])) {
                return TRUE;
            }
        }
        return FALSE;
    }

}