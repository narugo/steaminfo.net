<?php

require_once PATH_TO_CORE . 'logging.php';

function error($code, $message = '')
{
    writeErrorLog('Error ' . $code . ' ' . $message);
    $view = new View();
    switch ($code) {
        case 400:
            header("HTTP/1.0 400 Bad Request");
            $view->renderErrorPage('400', 'Bad Request');
            break;
        case 401:
            header("HTTP/1.1 401 Unauthorized");
            $view->renderErrorPage('401', 'Unauthorized');
            break;
        case 403:
            header("HTTP/1.0 403 Forbidden");
            $view->renderErrorPage('403', 'Forbidden');
            break;
        case 404:
            header("HTTP/1.0 404 Not Found");
            $view->renderErrorPage('404', 'Not Found');
            break;
        case 503:
            header("HTTP/1.0 503 Service Unavailable");
            $view->renderErrorPage('503', 'Service Unavailable');
            break;
        case 500:
        default:
            header("HTTP/1.0 500 Internal Server Error");
            $view->renderErrorPage('500', 'Server Error');
            break;
    }
    die;
}