<?php

class API extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index($params = NULL)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        require_once PATH_TO_MODELS . 'api.php';
        $api_model = new API_Model();
        if (isset($params[0])) {
            if (isset($params[1])) {
                $this->view->interface = $api_model->getInterface($params[0]);
                $this->view->method = $api_model->getMethod($params[1]);
                $this->view->renderPage('api/method', 'API', array(), array());
            } else {
                $this->view->interface = $api_model->getInterface($params[0]);
                $this->view->renderPage('api/interface', 'API', array(), array());
            }
        } else {
            $this->view->interfaces = $api_model->getInterfaces();
            $this->view->renderPage('api/interfaces', 'API', array(), array());
        }
    }

}