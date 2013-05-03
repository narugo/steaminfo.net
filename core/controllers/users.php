<?php

class Users extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $this->view->top = $users_model->getTop10();
        $this->view->renderPage('users/index', 'Users', array(), array(CSS_FONT_AWESOME, CSS_USERS));
    }

    function profile($params)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $this->view->user = $users_model->getUser($params[0]);
        if (empty($this->view->user)) error(404, "Profile not found");
        writeUserViewLog($this->view->user->getId());
        $this->view->renderPage('users/profile', $this->view->user->getNickname(),
            array(JS_TABLESORTER),
            array(CSS_FONT_AWESOME, CSS_USERS));
    }

    function apps($params)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
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
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
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

    function search()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $result = $users_model->search(trim($_GET['q']));
        header('Content-type: application/json');
        $response = null;
        if (is_a($result, 'SteamInfo\Models\Entities\User')) {
            $response = array(
                'id' => $result->getId(),
                'nickname' => $result->getNickname(),
                'avatar_url' => $result->getAvatarUrl(),
                'tag' => $result->getTag(),
                'status' => $result->getStatus(),
                'current_app_id'=>$result->getCurrentAppId(),
                'current_app_name'=>$result->getCurrentAppName()
            );
        }
        echo json_encode($response);
    }

    function searchSuggest()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $result = $users_model->getSearchSuggestions(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

}