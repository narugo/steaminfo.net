<?php

class Users extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->required_js = array(JS_JQUERY, JS_BOOTSTRAP, JS_TABLESORTER);
        $this->required_css = array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN, CSS_USERS);
    }

    function index($params = NULL)
    {
        if (empty($params)) {
            $this->view->renderPage("users/index", 'Steam Info - Users', $this->required_js, $this->required_css);
        } else {
            $users_model = getModel('users');
            $this->view->profile = $users_model->getProfileSummary($params[0]);
            writeUserViewLog($this->view->profile->getCommunityId());
            $this->view->renderPage(
                "users/profile",
                $this->view->profile->getNickname(),
                $this->required_js,
                $this->required_css
            );
        }
    }

    function apps($params)
    {
        $apps_model = getModel('apps');
        $this->view->apps = $apps_model->getOwnedApps($params[0]);
        if (!empty($this->view->apps)) {
            $this->view->renderPage("users/includes/apps", 'Steam Info - Users - Apps', $this->required_js, $this->required_css, TRUE);
        } else {
            echo "No apps!";
        }
    }

    function friends($params)
    {
        $users_model = getModel('users');
        $this->view->friends = $users_model->getFriends($params[0]);
        if (!empty($this->view->friends)) {
            $this->view->renderPage("users/includes/friends", 'Steam Info - Users - Friends', $this->required_js, $this->required_css, TRUE);
        } else {
            echo "No friends!";
        }
    }

    function groups($params)
    {
        $groups_model = getModel('groups');
        $this->view->groups = $groups_model->getUserGroups($params[0]);
        $this->view->renderPage("users/includes/groups", 'Steam Info - Users - Groups',
            $this->required_js, $this->required_css, TRUE);
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