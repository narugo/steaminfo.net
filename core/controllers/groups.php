<?php

class Groups extends Controller
{

    function __construct()
    {
        parent::__construct();
        require_once PATH_TO_MODELS . 'groups.php';
        $this->groups_model = new Groups_Model();
    }

    function index($params = NULL)
    {
        $required_js = array(JS_JQUERY, JS_BOOTSTRAP);
        $required_css = array(CSS_BOOTSTRAP, CSS_FONT_AWESOME, CSS_MAIN);
        if (empty($params)) {
            $this->view->renderPage("groups/index", 'Groups', $required_js, $required_css);
        } else {
            $this->view->group = $this->groups_model->getGroup($params[0]);
            writeGroupViewLog($this->view->group->getId());
            $this->view->renderPage(
                "groups/info",
                $this->view->group->getName() . 'Groups',
                $required_js,
                $required_css
            );
        }
    }

    function search()
    {
        $result = $this->groups_model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

    function valve()
    {
        echo "Updating tags of Valve employees...<br />";
        return $this->groups_model->updateValveEmployeeTags();
    }

}