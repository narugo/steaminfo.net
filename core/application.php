<?php

define('MODE_WEB', 'web');
define('MODE_CMD', 'cmd');

/**
 * Main modules
 */
require CORE_DIR . 'database.php';
require CORE_DIR . 'logging.php';
require CORE_DIR . 'error_handling.php';
require CORE_DIR . 'controller.php';
require CORE_DIR . 'view.php';
require CORE_DIR . 'model.php';
require CORE_DIR . 'assets.php';

/**
 * Main application class
 */
class Application
{

    function __construct($mode, $argv = NULL)
    {
        switch ($mode) {
            case MODE_CMD:
                self::runTool($argv);
                break;
            case MODE_WEB:
            default:
                self::runWeb();
                break;
        }
    }

    private function runTool($argv)
    {
        if (count($argv) < 2) {
            echo "No arguments!\n";
            return FALSE;
        }

        // TODO: Add commands
        switch ($argv[1]) {
            default:
                echo "Unknown argument!\n";
        }
    }

    private function runWeb()
    {
        // Getting requested path
        $path = $_SERVER['REQUEST_URI'];

        // Parsing url
        $path = rtrim($path, '/');
        $path = filter_var($path, FILTER_SANITIZE_URL);
        $path = explode('/', $path);
        $path = array_splice($path, 1);

        session_start();
        self::loadController($path);
    }

    private function loadController($path)
    {
        if (!isset($path[0])) {
            self::showHomepage();
        } else {
            $controller_path = PATH_TO_CONTROLLERS . $path[0] . '.php';
            if (file_exists($controller_path)) {
                require $controller_path;
                $controller = new $path[0];

                // Checking if method has been requested
                if (isset($path[1])) {
                    if (is_callable(array($controller, $path[1])) AND $path[1] != '__construct') {
                        $method = $path[1];
                        // Removing controller and method names
                        $params = array_splice($path, 2);
                        $controller->{$method}($params);
                    } else {
                        // Removing controller name
                        $params = array_splice($path, 1);
                        $controller->index($params);
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