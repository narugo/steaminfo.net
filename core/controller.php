<?php

define('PATH_TO_CONTROLLERS', CORE_DIR . 'controllers/');

class Controller
{

    function __construct()
    {
        $this->view = new View();
    }

}
