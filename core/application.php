<?php
require 'core/controller.php';
require 'core/view.php';
require 'core/database.php';
require 'core/model.php';

/**
 * Libraries
 */
require 'core/libs/steam-locomotive/locomotive.php';

/**
 * Main application class
 * Parses requested path, calls controllers and methods, passes parameters
 */
class Application {

    function __construct() {

        // Getting requested path
        $path = $_GET['path'];

        if (! isset($path)) {
            // Showing index page
            require 'core/controllers/index.php';
            $controller = new Index();
            $controller->index();
        } else {
            // Parsing url
            $path = rtrim($path, '/');
            $path = filter_var($path, FILTER_SANITIZE_URL);
            $path = explode('/', $path);

            // Trying to load controller
            $controller_path = 'core/controllers/'.$path[0].'.php';
            if (file_exists($controller_path)) {
                require $controller_path;
                $controller = new $path[0];

                // Checking if method has been requested
                if (isset($path[1])) {
                    $method = $path[1];
                    // TODO: Check if method is public
                    if (method_exists($controller, $method)) {
                        // Checking if additional parameters exist
                        if (isset($path[2])) {
                            // Removing controller and method names
                            $params = array_splice($path, 2);
                            // And passing parameters to method
                            $controller->{$method}($params);
                        } else {
                            $controller->{$method}();
                        }
                    } else {
                        $this->error();
                    }
                } else {
                    $controller->index();
                }
            } else {
                $this->error();
            }
        }
    }

    function error() {
        require 'controllers/error.php';
        $controller = new Error(404);
        $controller->index();
    }

}