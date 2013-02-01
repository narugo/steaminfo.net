<?php

class Dota extends Controller
{

    function __construct()
    {
        parent::__construct();

    }

    function index()
    {
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();
        $this->view->live_matches = $dota_model->getLiveLeagueMatches();
        $this->view->league = $dota_model->getLeagueListing();

        $this->view->renderPage('dota/index', 'Dota 2',
            array(JS_JQUERY),
            array(CSS_BOOTSTRAP, CSS_MAIN, CSS_DOTA));
    }

    function match($params)
    {
        $match_id = $params[0];
        if (!is_numeric($match_id)) error(400, 'Match ID is incorrect');
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();
        $response = $dota_model->getMatchDetails($match_id);
        if ($response['status'] === STATUS_SUCCESS) {
            writeMatchViewLog($match_id);
            $this->view->match = $response['match'];
            $this->view->players = $response['players'];
            $this->view->renderPage("dota/match", 'Match ' . $this->view->match->id . ' - Dota 2',
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
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();
        $dota_model->updateHeroes();
        echo 'Done.';
    }

}