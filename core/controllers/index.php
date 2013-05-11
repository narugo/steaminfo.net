<?php

class Index extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        $this->view->renderPage("index/index", NULL, array(), array(CSS_INDEX));
    }

    function searchSuggest()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);

        $query = $_GET['query'];

        $result = array();

        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $users_suggestions = $users_model->getSearchSuggestions($query, 5);
        foreach ($users_suggestions as $user_suggestion) {
            $current_suggestion = array(
                'type' => 'user',
                'id' => $user_suggestion->getId(),
                'name' => $user_suggestion->getNickname(),
                'avatar_url' => $user_suggestion->getAvatarUrl()
            );
            array_push($result, $current_suggestion);
        }

        require_once PATH_TO_MODELS . 'apps.php';
        $apps_model = new Apps_Model();
        $apps_suggestions = $apps_model->getSearchSuggestions($query, 5);
        foreach ($apps_suggestions as $app_suggestion) {
            $current_suggestion = array(
                'type' => 'app',
                'id' => $app_suggestion->getId(),
                'name' => $app_suggestion->getName()
            );
            array_push($result, $current_suggestion);
        }

        header('Content-type: application/json');
        echo json_encode($result);
    }

}
