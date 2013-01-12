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
        $this->view->profile = $users_model->getProfileSummary($this->view->id);
        writeUserViewLog($this->view->profile->getCommunityId());
        $this->view->renderPage(
            "users/profile",
            $this->view->profile->getNickname(),
            $this->required_js,
            $this->required_css
        );
    }

    function apps($params)
    {
        $apps_model = getModel('apps');
        $response = $apps_model->getUserApps($params[0]);
        if ($response['status'] === STATUS_SUCCESS) {
            $this->view->apps = $response['result'];
            if (!empty($this->view->apps)) {
                $this->view->renderPage("users/includes/apps", 'Apps', $this->required_js, $this->required_css, TRUE);
            } else {
                echo "No apps!";
            }
        } elseif ($response['status'] === STATUS_PRIVATE) {
            echo "Profile is private!";
        } else {
            echo "Unknown error.";
        }
    }

    function friends($params)
    {
        $users_model = getModel('users');
        $response = $users_model->getFriends($params[0]);
        if ($response['status'] === STATUS_SUCCESS) {
            $this->view->friends = $response['result'];
            if (!empty($this->view->friends)) {
                $this->view->renderPage("users/includes/friends", 'Friends', $this->required_js, $this->required_css, TRUE);
            } else {
                echo "No friends!";
            }
        } elseif ($response['status'] === STATUS_PRIVATE) {
            echo "Profile is private!";
        } else {
            echo "Unknown error.";
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