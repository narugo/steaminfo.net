<?php

require_once CORE_DIR . 'logging.php';

function error($http_response_code, $message = '')
{
    writeErrorLog('Error ' . $http_response_code . ' ' . $message);

    http_response_code($http_response_code);

    $view = new View();
    $view->renderErrorPage($http_response_code, "Error " + $http_response_code, $message);

    die;
}