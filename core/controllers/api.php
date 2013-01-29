<?php

class API extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $api_model = getModel('api');
        $this->view->api = $api_model->getAPI();
        $this->view->renderPage("api/index", 'Steam Info - API',
            array(),
            array(CSS_BOOTSTRAP, CSS_MAIN));
    }

}