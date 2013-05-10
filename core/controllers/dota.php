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
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();

        if (isset($params[0])) {
            $match_id = $params[0];
            if (!is_numeric($match_id)) error(400, 'Match ID is incorrect');
            /** @var \SteamInfo\Models\Entities\DotaMatch team */
            $this->view->match = $dota_model->getMatchDetails($match_id);
            $this->view->renderPage("dota/match", 'Match ' . $this->view->match->getId() . ' - Dota 2', array(), array(CSS_DOTA));
        } else {
            $this->view->live_matches = $dota_model->getLiveLeagueMatches();
            $this->view->renderPage("dota/matches", 'Matches - Dota 2', array(), array(CSS_DOTA));
        }
    }

    function leagues()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();
        $this->view->leagues = $dota_model->getLeagueListing();
        $this->view->renderPage("dota/leagues", 'Leagues - Dota 2', array(), array(CSS_DOTA));
    }

    function teams($params)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        if (!empty($params)) {
            $team_id = $params[0];
            if (!is_numeric($team_id)) error(400, 'Team ID is incorrect');
            require_once PATH_TO_MODELS . 'dota.php';
            $dota_model = new Dota_Model();
            /** @var \SteamInfo\Models\Entities\DotaTeam team */
            $this->view->team = $dota_model->getTeamDetails($team_id);
            $this->view->renderPage("dota/team", $this->view->team->getName() . ' - Teams - Dota 2',
                array(), array(CSS_DOTA));
        } else {
            $this->view->renderPage("dota/teams", 'Teams - Dota 2', array(), array(CSS_DOTA));
        }
    }

    function updateHeroesList()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') error(405);
        require_once PATH_TO_MODELS . 'dota.php';
        $dota_model = new Dota_Model();
        $dota_model->updateHeroes();
        echo 'Done.';
    }

}