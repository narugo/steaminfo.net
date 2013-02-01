<?php

class Auth extends Controller
{

    function __construct()
    {
        parent::__construct();

    }

    function index()
    {
        if (!empty($_SESSION['id'])) {
            header('Location: ' . WEBSITE_URL . 'control/');
        } else {
            try {
                $openid = new LightOpenID(HOSTNAME);
                if (!$openid->mode) {
                    $openid->identity = 'https://steamcommunity.com/openid/';
                    header('Location: ' . $openid->authUrl());
                } elseif ($openid->mode == 'cancel') {
                    echo 'Auth cancelled!';
                } else {
                    if ($openid->validate()) {
                        $_SESSION['id'] = str_replace('http://steamcommunity.com/openid/id/', '', $openid->identity);
                        header('Location: ' . WEBSITE_URL . 'auth/');
                    } else {
                        echo 'You are NOT logged in!';
                    }
                }
            } catch (ErrorException $e) {
                error(500, $e);
            }
        }
    }

    function logout()
    {
        unset($_SESSION['id']);
        header('Location: ' . WEBSITE_URL);
    }

}