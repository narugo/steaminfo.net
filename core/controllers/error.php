<?php

class Error extends Controller {

    function __construct($http_status_code, $message = NULL) {
        parent::__construct();
        $this->view->message = $message;
        $this->http_status = $http_status_code;
    }

    public function index() {
        // TODO: Show more interesting error messages
        switch ($this->http_status) {
            case 404:
                header("HTTP/1.0 404 Not Found");
                break;
            case 403:
                header("HTTP/1.0 403 Forbidden");
                break;
            default:
                header("HTTP/1.0 500 Internal Server Error");
                break;
        }
    }

}
