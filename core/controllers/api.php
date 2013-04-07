<?php

class API extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        require_once PATH_TO_MODELS . 'api.php';
        $api_model = new API_Model();
        $this->view->api = $api_model->getAPI();
        $this->view->renderPage('api/index', 'API', array(), array());
    }

}