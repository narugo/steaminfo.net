<?php

class Groups extends Controller {

    function __construct() {
        parent::__construct();
        $this->loadModel("groups");
    }

    function index() {
        $query = trim($_GET['q']);
        if (empty($query)) {
            $this->view->render("groups/index");
        } else {
            $this->view->info = $this->model->getGroupInfo($query);
            $this->view->render("groups/info");
        }
    }

    function info($params) {
        // TODO: Implement
        $this->view->render("groups/index");
    }

}