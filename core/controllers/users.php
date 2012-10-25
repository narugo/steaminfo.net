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
            $result = $this->model->search($query);
            if (count($result) < 1) { // Nothing has been found
                $this->view->render("users/noresults");
            } elseif (count($result) == 1) { // One result
                // TODO: Fix redirecting
                //header("Location: http://steaminfo.net/users/profile/" + $this->view->result->steamid); // Redirect browser
                //exit();
                self::profile(array($result[0]->steamid));
            } else { // More than one result
                $this->view->render("users/search");
            }
        }
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

}