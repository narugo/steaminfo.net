<?php

class Dota extends Controller
{

    function __construct()
    {
        parent::__construct();

    }

    function index()
    {
        error(404);
    }

    function matches($params)
    {
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();

        if (isset($params[0])) {
            $match_id = $params[0];
            if (!is_numeric($match_id)) error(400, 'Match ID is incorrect');
            $response = $dota_model->getMatchDetails($match_id);
            writeMatchViewLog($match_id);
            $this->view->match = $response['match'];
            $this->view->players = $response['players'];
            $this->view->renderPage("dota/match", 'Match ' . $this->view->match->id . ' - Dota 2', array(), array(CSS_DOTA));
        } else {
            $this->view->live_matches = $dota_model->getLiveLeagueMatches();
            $this->view->renderPage("dota/matches", 'Matches - Dota 2', array(), array(CSS_DOTA));
        }
    }

    function leagues()
    {
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();
        $this->view->league = $dota_model->getLeagueListing();
        $this->view->renderPage("dota/leagues", 'Matches - Dota 2', array(), array(CSS_DOTA));
    }

    function teams($params)
    {
        $team_id = $params[0];
        if (!is_numeric($team_id)) error(400, 'Team ID is incorrect');
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();
        $this->view->team = $dota_model->getTeamDetails($team_id);
        $this->view->renderPage("dota/team", $this->view->team->tag . ' - Teams - Dota 2',
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