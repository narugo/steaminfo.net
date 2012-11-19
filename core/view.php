<?php

class View
{
    /**
     * Prints contents of specific view
     * @param $name Name of view to print
     * @return bool Returns "TRUE" if view has been found, "FALSE" if not
     */
    public function render($name, $page_title, $js = array(), $css = array()) {
        $view_path = "core/views/$name.php";
        if (file_exists($view_path)) {
            require "core/views/header.php";
            require $view_path;
            require "core/views/footer.php";
            return true;
        } else {
            require 'core/controllers/error.php';
            $controller = new Error(404, "View not found!");
            $controller->index();
            return false;
        }
    }

}
