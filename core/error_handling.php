<?php

require_once PATH_TO_CORE . 'logging.php';

function error($code, $message = '')
{
    write_log_to_db('Error ' . $code . ' ' . $message);
    $view = new View();
    switch ($code) {
        case 403:
            header("HTTP/1.0 403 Forbidden");
            $view->renderError('403', 'Forbidden');
            break;
        case 404:
            header("HTTP/1.0 404 Not Found");
            $view->renderError('404', 'Not Found');
            break;
        case 500:
        default:
            header("HTTP/1.0 500 Internal Server Error");
            $view->renderError('500', 'Server Error');
            break;
    }
    die;
}