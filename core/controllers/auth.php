<?php

class Auth extends Controller
{

    function __construct()
    {
        parent::__construct();

    }

    function index()
    {
        session_start();
        if (!empty($_SESSION['id'])) {
            header('Location: https://steaminfo.net/control/');
        } else {
            try {
                $openid = new LightOpenID('steaminfo.net');
                if (!$openid->mode) {
                    $openid->identity = 'https://steamcommunity.com/openid/';
                    header('Location: ' . $openid->authUrl());
                } elseif ($openid->mode == 'cancel') {
                    echo "Auth cancelled!";
                } else {
                    if ($openid->validate()) {
                        $_SESSION['id'] = $openid->identity;
                        header('Location: https://steaminfo.net/auth/');
                    } else {
                        echo "You are NOT logged in!";
                    }
                }
            } catch (ErrorException $e) {
                error(500, $e);
            }
        }
    }

    function logout()
    {
        session_start();
        unset($_SESSION['id']);
        header('Location: https://steaminfo.net/');
    }

    function id()
    {
        session_start();
        var_dump($_SESSION['id']);
    }

}