<?php

class View
{
    /**
     * Prints contents of specific view
     * @param $name Name of view to print
     * @param bool $noIncludes "TRUE" if header and footer needs to be loaded, "FALSE" owerwise
     * @return bool Returns "TRUE" if view has been found, "FALSE" if not
     */
    public function render($name, $noIncludes = FALSE) {
        $view_path = "core/views/$name.php";
        if (file_exists($view_path)) {
            if (!$noIncludes) {
                require "core/views/header.php";
                require $view_path;
                require "core/views/footer.php";
            } else {
                require $view_path;
            }
            return true;
        } else {
            require 'core/controllers/error.php';
            $controller = new Error(404, "View not found!");
            $controller->index();
            return false;
        }
    }

}
