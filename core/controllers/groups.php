<?php

class Groups extends Controller {

    function __construct() {
        parent::__construct();
        $this->loadModel("groups");

        $this->required_js = array('jquery', 'bootstrap', 'spin.min');
        $this->required_css = array('bootstrap.min', 'screen');
    }

    function index() {
        $this->view->render("groups/index", 'Groups - Steam Info', $this->required_js, $this->required_css);
    }

    function search() {
        $result = $this->model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function searchSuggest() {}

    function valve() {
        echo "Updating tags of Valve employees...<br />";
        return $this->model->updateValveEmployeeTags();
    }

}