<?php

class Apps extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index($params = NULL)
    {
        if (empty($params)) {
            $this->view->renderPage("apps/index", 'Apps',
                array(), array(CSS_FONT_AWESOME, CSS_APPS));
        } else {
            require_once PATH_TO_MODELS . 'apps.php';
            $apps_model = new Apps_Model();
            $this->view->app = $apps_model->getApp($params[0]);
            if (!empty($this->view->app)) {
                $this->view->renderPage("apps/info", $this->view->app->name . ' - Apps',
                    array(), array(CSS_FONT_AWESOME, CSS_APPS)
                );
            } else {
                error(404, 'App not found');
            }
        }
    }

    function search()
    {
        require_once PATH_TO_MODELS . 'apps.php';
        $apps_model = new Apps_Model();
        $result = $apps_model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

}