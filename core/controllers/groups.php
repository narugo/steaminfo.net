<?php

class Groups extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->loadModel("groups");

        $this->required_js = array(JS_JQUERY, JS_BOOTSTRAP, JS_SPIN);
        $this->required_css = array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN);
    }

    function index()
    {
        $this->view->render("groups/index", 'Groups - Steam Info', $this->required_js, $this->required_css);
    }

    function search()
    {
        $result = $this->model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function searchSuggest()
    {
    }

    function valve()
    {
        echo "Updating tags of Valve employees...<br />";
        return $this->model->updateValveEmployeeTags();
    }

}