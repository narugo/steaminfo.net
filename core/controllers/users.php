<?php

class Users extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->required_js = array(JS_JQUERY, JS_BOOTSTRAP, JS_TABLESORTER);
        $this->required_css = array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN, CSS_USERS);
    }

    function index()
    {
        $this->view->renderPage("users/index", 'Users', $this->required_js, $this->required_css);
    }

    function profile($params)
    {
        $users_model = getModel('users');
        $this->view->id = $params[0];
        $this->view->profile = $users_model->getProfile($this->view->id);
        if ($this->view->profile === FALSE OR $this->view->profile->getNickname() == '') {
            $this->view->renderPage(
                "users/not_indexed",
                $this->view->id,
                $this->required_js,
                $this->required_css
            );
        } else {
            $this->view->renderPage(
                "users/profile",
                $this->view->profile->getNickname(),
                $this->required_js,
                $this->required_css
            );
        }
    }

    function update()
    {
        $users_model = getModel('users');
        $community_id = $_GET['id'];
        if (is_null($community_id)) error(400, 'Community ID not supplied');
        if ($users_model->updateProfile($community_id) === TRUE) {
            header("HTTP/1.0 200 OK");
            echo "HTTP/1.0 200 OK";
        } else {
            header("HTTP/1.0 500 Internal Server Error");
            echo "HTTP/1.0 500 Internal Server Error";
        }
    }

    function apps($params)
    {
        $apps_model = getModel('apps');
        $this->view->apps = $apps_model->getAppsForUser($params[0]);
        $this->view->renderPage("users/includes/apps", 'Apps', $this->required_js, $this->required_css, TRUE);
    }

    function friends($params)
    {
        $users_model = getModel('users');
        $this->view->friends = $users_model->getFriends($params[0]);
        if (!empty($this->view->friends)) {
            $this->view->renderPage("users/includes/friends", 'Friends', $this->required_js, $this->required_css, TRUE);
        } else {
            echo "No friends!";
        }
    }

    function groups($params)
    {
        $groups_model = getModel('users');
        $this->view->groups = $groups_model->getGroups($params[0]);
        if (is_null($this->view->groups)) {

        } else {
            $this->view->renderPage("users/includes/groups", 'Groups', $this->required_js, $this->required_css, TRUE);
        }
    }

    function search()
    {
        $users_model = getModel('users');
        $result = $users_model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function searchSuggest()
    {
        $users_model = getModel('users');
        $result = $users_model->getSearchSuggestions(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

}