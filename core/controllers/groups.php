<?php

class Groups extends Controller
{

    function __construct()
    {
        parent::__construct();

    }

    function index()
    {
        $this->required_js = array(JS_JQUERY, JS_BOOTSTRAP);
        $this->required_css = array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN);
        $this->view->renderPage("groups/index", 'Groups', $this->required_js, $this->required_css);
    }

    function search()
    {
        $groups_model = getModel('groups');
        $result = $groups_model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function searchSuggest()
    {
    }

    function valve()
    {
        echo "Updating tags of Valve employees...<br />";
        $groups_model = getModel('groups');
        return $groups_model->updateValveEmployeeTags();
    }

}