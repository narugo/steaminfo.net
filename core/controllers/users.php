<?php

class Users extends Controller {

    function __construct() {
        parent::__construct();
        $this->loadModel("users");

        $this->required_js = array('jquery', 'bootstrap', 'jquery.tablesorter.min', 'spin.min');
        $this->required_css = array('bootstrap.min', 'screen', 'users');
    }

    function index() {
        $this->view->render("users/index", 'Users - Steam Info', $this->required_js, $this->required_css);
    }

    function profile($params) {
        // TODO: Check if info is loaded second time (that shouldn't happen)
        $response = $this->model->getProfile($params[0]);
        $this->view->profile = $response['profile'];
        $this->view->update_status = $response['update_status'];
        $this->view->render("users/profile",
            $this->view->profile->getNickname() . ' - Steam Info',
            $this->required_js,
            $this->required_css);
    }

    function apps($params) {
        $response = $this->model->getApps($params[0]);
        $this->view->apps = $response['apps'];
        $this->view->update_status = $response['update_status'];
        $this->view->render("users/includes/apps", 'Apps', $this->required_js, $this->required_css);
    }

    function friends($params) {
        $response = $this->model->getFriends($params[0]);
        $this->view->friends = $response['friends'];
        $this->view->update_status = $response['update_status'];
        $this->view->render("users/includes/friends", 'Friends', $this->required_js, $this->required_css);
    }

    function groups($params) {
        $this->view->groups = $this->model->getGroups($params[0]);
        $this->view->render("users/includes/groups", 'Groups', $this->required_js, $this->required_css);
    }

    function search() {
        $result = $this->model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function searchSuggest() {}

}