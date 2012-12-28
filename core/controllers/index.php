<?php

class Index extends Controller
{

    function __construct()
    {
        parent::__construct();

        $this->required_js = array(JS_JQUERY, JS_JQUERY_UI, JS_BOOTSTRAP, JS_SPIN);
        $this->required_css = array(CSS_JQUERY_UI, CSS_BOOTSTRAP, CSS_MAIN, CSS_INDEX);
    }

    public function index()
    {
        $this->view->render("index", 'Steam Info', $this->required_js, $this->required_css);
    }

    function search()
    {
        // TODO: Get search results from users and groups
        $result = NULL;
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function searchSuggest()
    {
        $result = array();
        // Getting search suggestions from users
        $users_model = $this->getModel("users");
        $users_suggestions = $users_model->getSearchSuggestions($_GET['q']);
        foreach ($users_suggestions as $user_suggestion) {
            $user_suggestion["type"] = 'user';
            array_push($result, $user_suggestion);
        }
        header('Content-type: application/json');
        echo json_encode($result);
    }

}
