<?php

class Dota extends Controller
{

    function __construct()
    {
        parent::__construct();

    }

    function index()
    {
        $this->view->renderPage("dota/index", 'Dota 2',
            array(JS_JQUERY),
            array(CSS_BOOTSTRAP, CSS_MAIN, CSS_DOTA));

    }

    function match($params)
    {
        $match_id = $params[0];
        if (!is_numeric($match_id)) error(400, 'Match ID is incorrect');
        $dota_model = getModel('dota');
        $response = $dota_model->getMatchDetails($match_id);
        if ($response['status'] === STATUS_SUCCESS) {
            $this->view->match = $response['match'];
            $this->view->players = $response['players'];
            $this->view->renderPage("dota/match", 'Dota 2',
                array(JS_JQUERY, JS_BOOTSTRAP),
                array(CSS_BOOTSTRAP, CSS_MAIN, CSS_DOTA));
        } else if ($response['status'] === STATUS_UNAUTHORIZED) {
            error(401, 'Can\'t view info about this match');
        } else if ($response['status'] === STATUS_API_UNAVAILABLE) {
            error(503, 'Steam API is unavailable');
        }
    }

    function updateHeroesList()
    {
        $dota_model = getModel('dota');
        $dota_model->updateHeroes();
        echo 'Done.';
    }

}