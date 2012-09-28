<?php

class Controller {

    function __construct() {
        $this->view = new View();
    }

    /**
     * Loads specific model into $model variable
     * @param $name Model's name
     */
    public function loadModel($name) {
        $path = 'core/models/'.$name.'.php';
        if (file_exists($path)) {
            require $path;
            $model_name = $name.'_Model';
            $this->model = new $model_name();
        } else {
            // TODO: Handle this properly
            echo "Model <em>$path</em> not found!";
        }
    }

}
