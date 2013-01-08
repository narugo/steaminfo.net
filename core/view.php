<?php

define('PATH_TO_VIEWS', PATH_TO_CORE . 'views/');

class View
{

    public function render($name, $page_title, $js = array(), $css = array())
    {
        $view_path = PATH_TO_VIEWS . $name . '.php';
        if (file_exists($view_path)) {
            self::includeView($view_path, $page_title, FALSE, $js, $css);
        } else {
            error(404, 'View not found');
        }
    }

    public function renderError($error_id, $page_title, $js = array(), $css = array())
    {
        define('PATH_TO_ERROR_VIEWS', PATH_TO_VIEWS . 'errors/');
        $view_path = PATH_TO_ERROR_VIEWS . $error_id . '.php';
        if (file_exists($view_path)) {
            self::includeView($view_path, $page_title);
        } else {
            write_log_to_db('Error view not found: "' . $view_path . '"');
        }
    }

    private function includeView($view_path, $page_title, $no_header_footer = TRUE, $js = array(), $css = array())
    {
        if (!$no_header_footer) require PATH_TO_VIEWS . 'header.php';
        require $view_path;
        if (!$no_header_footer) require PATH_TO_VIEWS . 'footer.php';
    }

}
