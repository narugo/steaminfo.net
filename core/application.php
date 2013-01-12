<?php

define(PATH_TO_LIBS, PATH_TO_CORE . 'libs/');

/**
 * Main modules
 */
require_once PATH_TO_CORE . 'logging.php';
require_once PATH_TO_CORE . 'error_handling.php';
require_once PATH_TO_CORE . 'controller.php';
require_once PATH_TO_CORE . 'view.php';
require_once PATH_TO_CORE . 'database.php';
require_once PATH_TO_CORE . 'model.php';

// Assets
require_once PATH_TO_CORE . 'assets.php';

// Libraries
require_once PATH_TO_LIBS . 'libs.php';

/**
 * Main application class
 * Parses requested path, calls controllers and methods, passes parameters
 */
class Application
{

    function __construct()
    {
        // Getting requested path
        $path = $_SERVER['REQUEST_URI'];

        // Parsing url
        $path = rtrim($path, '/');
        $path = filter_var($path, FILTER_SANITIZE_URL);
        $path = explode('/', $path);
        $path = array_splice($path, 1);

        self::loadController($path);
    }

    private function loadController($path)
    {
        if (!isset($path[0])) {
            self::showHomepage();
        } else {
            $controller_name = $path[0];
            $controller_path = PATH_TO_CONTROLLERS . $controller_name . '.php';
            if (file_exists($controller_path)) {
                require $controller_path;
                $controller = new $path[0];

                // Checking if method has been requested
                $method = $path[1];
                if (isset($method)) {
                    // TODO: Check if method is public
                    if (method_exists($controller, $method)) {
                        // Removing controller and method names
                        $params = array_splice($path, 2);
                        $controller->{$method}($params);
                    } else {
                        error(404, 'Method not found');
                    }
                } else {
                    $controller->index();
                }
            } else {
                error(404, 'Controller not found');
            }
        }
    }

    private function showHomepage()
    {
        require PATH_TO_CONTROLLERS . 'index.php';
        $controller = new Index();
        $controller->index();
    }

}