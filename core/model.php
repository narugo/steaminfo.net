<?php

define('PATH_TO_MODELS', PATH_TO_CORE . 'models/');

class Model {

    function __construct() {
        $this->db = new Database();
    }

}