<?php

define('PATH_TO_VIEWS', PATH_TO_CORE . 'views/');

class View
{

    public function render($name, $page_title, $js = array(), $css = array())
    {
        $view_path = PATH_TO_VIEWS . $name . '.php';
        if (file_exists($view_path)) {
            require PATH_TO_VIEWS . 'header.php';
            require $view_path;
            require PATH_TO_VIEWS . 'footer.php';
            return true;
        } else {
            require PATH_TO_CONTROLLERS . 'error.php';
            $controller = new Error(404, "View not found!");
            $controller->index();
            return false;
        }
    }

}
