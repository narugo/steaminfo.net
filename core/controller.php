<?php

define('PATH_TO_CONTROLLERS', PATH_TO_CORE . 'controllers/');

class Controller
{

    function __construct()
    {
        $this->view = new View();
    }

}
