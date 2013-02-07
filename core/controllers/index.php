<?php

class Index extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view->renderPage("index/index", NULL, array(), array(CSS_INDEX));
    }

    function searchSuggest()
    {
        $query = $_GET['query'];

        $result = array();

        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $users_suggestions = $users_model->getSearchSuggestions($query);
        foreach ($users_suggestions as $user_suggestion) {
            $user_suggestion["type"] = 'user';
            array_push($result, $user_suggestion);
        }

        require_once PATH_TO_MODELS . 'groups.php';
        $groups_model = new Groups_Model();
        $groups_suggestions = $groups_model->getSearchSuggestions($query);
        foreach ($groups_suggestions as $group_suggestion) {
            $group_suggestion["type"] = 'group';
            array_push($result, $group_suggestion);
        }

        require_once PATH_TO_MODELS . 'apps.php';
        $apps_model = new Apps_Model();
        $apps_suggestions = $apps_model->getSearchSuggestions($query);
        foreach ($apps_suggestions as $app_suggestion) {
            $app_suggestion["type"] = 'app';
            array_push($result, $app_suggestion);
        }

        header('Content-type: application/json');
        echo json_encode($result);
    }

}
