<?php

class Users extends Controller {

    function __construct() {
        parent::__construct();
        $this->loadModel("users");
    }

    function index() {
        $this->view->render("users/index");
    }

    function profile($params) {
        // TODO: Check if info is loaded second time (that shouldn't happen)
        $response = $this->model->getProfile($params[0]);
        $this->view->profile = $response['profile'];
        $this->view->update_status = $response['update_status'];
        $this->view->render("users/profile");
    }

    function apps($params) {
        $response = $this->model->getApps($params[0]);
        $this->view->apps = $response['apps'];
        $this->view->update_status = $response['update_status'];
        $this->view->render("users/includes/apps", TRUE);
    }

    function friends($params) {
        $response = $this->model->getFriends($params[0]);
        $this->view->friends = $response['friends'];
        $this->view->update_status = $response['update_status'];
        $this->view->render("users/includes/friends", TRUE);
    }

    function groups($params) {
        $this->view->groups = $this->model->getGroups($params[0]);
        $this->view->render("users/includes/groups", TRUE);
    }

    function search() {
        $result = $this->model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function searchSuggest() {}

}