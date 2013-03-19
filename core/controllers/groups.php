<?php

class Groups extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index($params = NULL)
    {
        require_once PATH_TO_MODELS . 'groups.php';
        $groups_model = new Groups_Model();
        if (empty($params)) {
            $this->view->top = $groups_model->getTop10();
            $this->view->renderPage("groups/index", 'Groups',
                array(), array(CSS_FONT_AWESOME, CSS_GROUPS));
        } else {
            $this->view->group = $groups_model->getGroup($params[0]);
            writeGroupViewLog($this->view->group->getId());
            $this->view->renderPage("groups/info", $this->view->group->getName() . 'Groups',
                array(), array(CSS_FONT_AWESOME, CSS_GROUPS)
            );
        }
    }

    function search()
    {
        require_once PATH_TO_MODELS . 'groups.php';
        $groups_model = new Groups_Model();
        $result = $groups_model->search(trim($_GET['q']));
        header('Content-type: application/json');
        echo json_encode($result);
    }

}