<?php

function error($http_response_code, $message = '')
{
    // TODO: Write error log

    http_response_code($http_response_code);

    $view = new View();
    $view->renderErrorPage($http_response_code, "Error " + $http_response_code, $message);

    /* Will you please just */ die; /* already? */
}