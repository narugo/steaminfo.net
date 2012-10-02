<?php

class Users extends Controller {

    function __construct() {
        parent::__construct();
        $this->loadModel("users");
    }

    function index() {
        $query = trim($_GET['q']);
        if (empty($query)) {
            $this->view->render("users/index");
        } else {
            self::profile(array($query));
        }
    }

    function profile($params) {
        try {
            $response = $this->model->getProfile($params[0]);
            $this->view->profile = $response['profile'];
            $this->view->update_status = $response['update_status'];
            $this->view->render("users/profile");
        } catch (Exception $e) {

        }
    }

    function apps($params) {
        try {
            $response = $this->model->getApps($params[0]);
            $this->view->apps = $response['apps'];
            $this->view->update_status = $response['update_status'];
            $this->view->render("users/includes/apps", TRUE);
        } catch (Exception $e) {

        }
    }

    function friends($params) {
        try {
            $response = $this->model->getFriends($params[0]);
            $this->view->friends = $response['friends'];
            $this->view->update_status = $response['update_status'];
            $this->view->render("users/includes/friends", TRUE);
        } catch (Exception $e) {

        }
    }

    function groups($params) {
        try {
            $this->view->groups = $this->model->getGroups($params[0]);
            $this->view->render("users/includes/groups", TRUE);
        } catch (Exception $e) {

        }
    }

    function search() {
        // TODO: Implement search
    }

}