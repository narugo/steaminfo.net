<?php

class Users extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index($params = NULL)
    {
        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        if (empty($params)) {
            $this->view->top = $users_model->getTop10();
            $this->view->renderPage('users/index', 'Users', array(), array(CSS_FONT_AWESOME, CSS_USERS));
        } else {
            $this->view->profile = $users_model->getProfileSummary($params[0]);
            writeUserViewLog($this->view->profile->getCommunityId());
            $this->view->renderPage('users/profile', $this->view->profile->getNickname(),
                array(JS_TABLESORTER),
                array(CSS_FONT_AWESOME, CSS_USERS));
        }
    }

    function apps($params)
    {
        require_once PATH_TO_MODELS . 'apps.php';
        $apps_model = new Apps_Model();
        $this->view->apps = $apps_model->getOwnedApps($params[0]);
        if (!empty($this->view->apps)) {
            $this->view->renderPage('users/includes/apps', 'Apps - Users',
                array(JS_TABLESORTER),
                array(CSS_FONT_AWESOME, CSS_USERS),
                TRUE);
        } else {
            echo 'No apps or profile is private.';
        }
    }

    function friends($params)
    {
        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $this->view->friends = $users_model->getFriends($params[0]);
        if (!empty($this->view->friends)) {
            $this->view->renderPage('users/includes/friends', 'Friends - Users',
                array(JS_TABLESORTER),
                array(CSS_FONT_AWESOME, CSS_USERS),
                TRUE);
        } else {
            echo 'No friends or profile is private.';
        }
    }

    function groups($params)
    {
        require_once PATH_TO_MODELS . 'groups.php';
        $groups_model = new Groups_Model();
        $this->view->groups = $groups_model->getUserGroups($params[0]);
        if (!empty($this->view->groups)) {
            $this->view->renderPage('users/includes/groups', 'Groups - Users',
                array(JS_TABLESORTER),
                array(CSS_FONT_AWESOME, CSS_USERS),
                TRUE);
        } else {
            echo 'No groups or profile is private.';
        }
    }

    function search()
    {
        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $result = $users_model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function searchSuggest()
    {
        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $result = $users_model->getSearchSuggestions(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

}