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

        $this->view->renderPage('dota/index', 'Dota 2', array(), array(CSS_DOTA));
    }

    function match($params)
    {
        $match_id = $params[0];
        if (!is_numeric($match_id)) error(400, 'Match ID is incorrect');
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();
        $response = $dota_model->getMatchDetails($match_id);
        writeMatchViewLog($match_id);
        $this->view->match = $response['match'];
        $this->view->players = $response['players'];
        $this->view->renderPage("dota/match", 'Match ' . $this->view->match->id . ' - Dota 2',
            array(), array(CSS_DOTA));
    }

    function updateHeroesList()
    {
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();
        $dota_model->updateHeroes();
        echo 'Done.';
    }

}