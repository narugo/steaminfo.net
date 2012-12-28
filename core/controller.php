<?php

define('PATH_TO_CONTROLLERS', PATH_TO_CORE . 'controllers/');

class ModelNotFoundException extends Exception
{
}

class Controller
{

    /**
     * Loading view
     */
    function __construct()
    {
        $this->view = new View();
    }

    /**
     * Loads specific model into $model variable
     * @param $name Model's name
     * @return mixed Returns TRUE if file has been found, FALSE otherwise
     */
    public function loadModel($name)
    {
        try {
            $this->model = self::getModel($name);
            return TRUE;
        } catch (ModelNotFoundException $e) {
            return FALSE;
        }
    }

    /**
     * @param $name Model's name
     * @return mixed New model object
     * @throws ModelNotFoundException
     */
    public function getModel($name)
    {
        $path = PATH_TO_MODELS. $name . '.php';
        if (file_exists($path)) {
            require $path;
            $model_name = $name . '_Model';
            return new $model_name();
        } else {
            throw new ModelNotFoundException();
        }
    }

}
