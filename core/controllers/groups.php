<?php

class Groups extends Controller {

    function __construct() {
        parent::__construct();
        $this->loadModel("groups");
    }

    function index() {
        $this->view->render("groups/index");
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