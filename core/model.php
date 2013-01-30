<?php

define('PATH_TO_MODELS', CORE_DIR . 'models/');

/**
 * Statuses
 */
define('STATUS_OUTDATED', 'outdated');
define('STATUS_SUCCESS', 'success');
define('STATUS_UNAUTHORIZED', 'unauthorized');
define('STATUS_API_UNAVAILABLE', 'private');
define('STATUS_UNKNOWN', 'unknown');

class ModelNotFoundException extends Exception
{
}

function getModel($name)
{
    $path = PATH_TO_MODELS . $name . '.php';
    if (file_exists($path)) {
        require_once $path;
        $model_name = $name . '_Model';
        return new $model_name();
    } else {
        throw new ModelNotFoundException();
    }
}

class Model
{

    function __construct()
    {
        $this->db = new Database();

        $this->memcached = new Memcached();
        $this->memcached->addServer(MEMCACHED_SERVER, MEMCACHED_PORT);

        $this->steam = new Locomotive();
    }

}