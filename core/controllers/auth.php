<?php

class Auth extends Controller
{

    function __construct()
    {
        parent::__construct();

    }

    function index()
    {
        try {
            $openid = new LightOpenID('steaminfo.net');
            if (!$openid->mode) {
                $openid->identity = 'https://steamcommunity.com/openid/';
                header('Location: ' . $openid->authUrl());
            } elseif ($openid->mode == 'cancel') {
                $this->view->renderPage("auth/cancelled", 'Auth',
                    array(JS_JQUERY),
                    array(CSS_BOOTSTRAP));
            } else {
                $this->view->openid = $openid;
                $this->view->renderPage("auth/result", 'Auth',
                    array(),
                    array(CSS_BOOTSTRAP, CSS_MAIN));
            }
        } catch (ErrorException $e) {
            echo $e->getMessage();
        }
    }

}