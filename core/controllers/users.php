<?php

class Users extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->loadModel("users");

        $this->required_js = array(JS_JQUERY, JS_BOOTSTRAP, JS_TABLESORTER);
        $this->required_css = array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN, CSS_USERS);
    }

    function index()
    {
        $this->view->render("users/index", 'Users', $this->required_js, $this->required_css);
    }

    function profile($params)
    {
        // TODO: Check if info is loaded second time (that shouldn't happen)
        try {
            $response = $this->model->getProfile($params[0]);
        } catch (WrongIDException $e) {
            error(404, 'Wrong ID');
        }
        $this->view->profile = $response['profile'];
        $this->view->update_status = $response['update_status'];
        $this->view->render("users/profile",
            $this->view->profile->getNickname() . ' - Steam Info',
            $this->required_js,
            $this->required_css);
    }

    function apps($params)
    {
        $response = $this->model->getApps($params[0]);
        $this->view->apps = $response['apps'];
        $this->view->update_status = $response['update_status'];
        $this->view->render("users/includes/apps", 'Apps', $this->required_js, $this->required_css);
    }

    function friends($params)
    {
        $response = $this->model->getFriends($params[0]);
        $this->view->friends = $response['friends'];
        $this->view->update_status = $response['update_status'];
        $this->view->render("users/includes/friends", 'Friends', $this->required_js, $this->required_css);
    }

    function groups($params)
    {
        $this->view->groups = $this->model->getGroups($params[0]);
        if         (is_null($this->view->groups)) {

        }         else {
        $this->view->render("users/includes/groups", 'Groups', $this->required_js, $this->required_css);
        }
    }

    function search()
    {
        $result = $this->model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function searchSuggest()
    {
        $result = $this->model->getSearchSuggestions(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

}