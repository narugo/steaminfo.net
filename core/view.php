<?php

define('PATH_TO_VIEWS', CORE_DIR . 'views/');

class View
{

    public function renderPage($name, $page_title = NULL, $js = array(), $css = array(), $no_header_footer = FALSE)
    {
        array_unshift($js, JS_JQUERY, JS_JQUERY_UI, JS_JQUERY_UI_AUTOCOMPLETE_HTML, JS_BOOTSTRAP);
        array_unshift($css, CSS_JQUERY_UI, CSS_BOOTSTRAP, CSS_FONT_ROBOTO, CSS_MAIN);
        $view_path = PATH_TO_VIEWS . $name . '.php';
        if (file_exists($view_path)) {
            self::includeView($view_path, $page_title, $no_header_footer, $js, $css);
        } else {
            error(404, 'View not found');
        }
    }

    private function includeView($view_path, $page_title = NULL, $no_header_footer = TRUE, $js = array(), $css = array())
    {
        if (empty($page_title)) {
            $page_title = 'Steam Info';
        } else {
            $page_title .= ' - Steam Info';
        }
        require PATH_TO_VIEWS . 'header.php';
        require $view_path;
        require PATH_TO_VIEWS . 'footer.php';
    }

    public function renderErrorPage($error_id, $page_title = NULL, $message = '')
    {
        define('PATH_TO_ERROR_VIEWS', PATH_TO_VIEWS . 'errors/');
        $view_path = PATH_TO_ERROR_VIEWS . $error_id . '.php';
        if (file_exists($view_path)) {
            if (empty($page_title)) {
                $page_title = $error_id;
            }
            self::includeView($view_path, $page_title, true);
        } else {
            writeErrorLog('Error view not found: "' . $view_path . '"');
        }
    }

}
