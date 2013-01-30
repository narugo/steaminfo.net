<?php

class Dota extends Controller
{

    function __construct()
    {
        parent::__construct();

    }

    function index()
    {
        $this->view->renderPage("dota/index", 'Dota 2 - Match Info',
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
            writeMatchViewLog($match_id);
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

    function test()
    {
// Configuration values

        define('WEBAPIKEY', '4BBF0EF97F856E40284A00B8B702E64C');

// Set up our cache backing
        $mc = new Memcached();
        $mc->addServer("localhost", 11211);

        function MakeTemplateImage()
        {
            $canvas = new Imagick();
            $canvas->newPseudoImage(500, 70, "gradient:#242C30-#000000");
            $canvas->borderImage("black", 1, 1);

            // Draw a box to put the logo in
            $logoColorBorder = new ImagickPixel("#555555");
            $logoBoxDraw = new ImagickDraw();
            $logoBoxDraw->setFillColor("#000000");
            $logoBoxDraw->setStrokeColor($logoColorBorder);
            $logoBoxDraw->setStrokeWidth(1);
            $logoBoxDraw->rectangle(-1, -1, 71, 72);
            $canvas->drawImage($logoBoxDraw);

            // Add a DOTA2.COM link
            $fontColorDark = new ImagickPixel("#656565");
            $urlDraw = new ImagickDraw();
            $urlDraw->setFontSize(9.1);
            $urlDraw->setFontWeight(900);
            $urlDraw->setFillColor($fontColorDark);

            $urlDraw->setTextAlignment(2 /* CENTER */);
            $urlDraw->annotation(36, 66, "DOTA2.COM");

            $canvas->drawImage($urlDraw);

            return $canvas;
        }


        $gif = new Imagick();
        $gif->setFormat("gif");

// Get the list of leagues from the cache or WebAPI.
        $leagueContents = $mc->get("league_contents");
        if ($leagueContents === false) {
            $leagueContents = file_get_contents("http://api.steampowered.com/IDOTA2Match_570/GetLeagueListing/V001/?language=en&key=" . WEBAPIKEY);
            $mc->set("league_contents", $leagueContents, 60 * 60 * 1 /* 1 hour */);
        }
        $data = json_decode($leagueContents, true);

// Construct a mapping from the league ID to names.
        $leagueIDToName = array();
        if (isset($data["result"]["leagues"])) {
            foreach ($data["result"]["leagues"] as $league) {
                $leagueIDToName[$league["leagueid"]] = $league["name"];
            }
        }

// Get the list of live league games from the cache or WebAPI.
        $liveGamesContents = $mc->get("live_games_contents");
        if ($liveGamesContents === false) {
            $liveGamesContents = file_get_contents("http://api.steampowered.com/IDOTA2Match_570/GetLiveLeagueGames/V001/?language=en&key=" . WEBAPIKEY);
            $mc->set("live_games_contents", $liveGamesContents, 60 * 3 /* 3 minutes */);
        }
        $data = json_decode($liveGamesContents, true);

        if (isset($data["result"]["games"]) && !empty($data["result"]["games"])) {
            foreach ($data["result"]["games"] as $game) {
                $canvas = MakeTemplateImage();

                $fontColorBright = new ImagickPixel("#EEEEDD");
                $fontColorDark = new ImagickPixel("#A2A2A2");
                $fontColor = new ImagickPixel("#BFBFBF");

                $draw = new ImagickDraw();
                $draw->setFontSize(22);
                $draw->setFontWeight(600);
                $draw->setFillColor($fontColorBright);

                $draw->setTextAlignment(2 /* CENTER */);
                $draw->annotation(277, 30, isset($leagueIDToName[$game["league_id"]]) ? $leagueIDToName[$game["league_id"]] : "Unknown League");

                $draw->setFontSize(18);
                $draw->setFontWeight(600);
                $draw->setFillColor($fontColor);

                $draw->setTextAlignment(1 /* LEFT */);
                $draw->annotation(95, 60, $game["dire_team"]["team_name"]);

                $draw->setTextAlignment(2 /* CENTER */);
                $draw->setFillColor($fontColorDark);
                $draw->setFontWeight(100);
                $draw->annotation(277, 60, "vs");
                $draw->setFontWeight(600);
                $draw->setFillColor($fontColor);

                $draw->setTextAlignment(3 /* RIGHT */);
                $draw->annotation(460, 60, $game["radiant_team"]["team_name"]);

                $canvas->drawImage($draw);

                $drawColorBorder = new ImagickPixel("#852222");

                // Draw a box to put the live text in
                $liveBoxDraw = new ImagickDraw();
                $liveBoxDraw->setFillColor("#000000");
                $liveBoxDraw->setStrokeColor($drawColorBorder);
                $liveBoxDraw->setStrokeWidth(1);
                $liveBoxDraw->rectangle(484, -1, 502, 72);

                $canvas->drawImage($liveBoxDraw);

                // Draw some text showing it's a live game
                $fontColorRed = new ImagickPixel("#FF1111");

                $liveDraw = new ImagickDraw();
                $liveDraw->setFontSize(9.1);
                $liveDraw->setFontWeight(900);
                $liveDraw->setFillColor($fontColorRed);

                $liveDraw->setTextAlignment(1 /* LEFT */);
                $liveDraw->rotate(270);
                $liveDraw->annotation(-67, 497, "LIVE MATCH!");

                $canvas->drawImage($liveDraw);


                $canvas->setImageFormat("gif");
                $canvas->setImageDelay(350);
                $gif->addImage($canvas);
            }
        } else {
            $canvas = MakeTemplateImage();

            $fontColor = new ImagickPixel("#777777");

            $draw = new ImagickDraw();
            $draw->setFontSize(22);
            $draw->setFontWeight(100);
            $draw->setFillColor($fontColor);

            $draw->setTextAlignment(2 /* CENTER */);
            $draw->annotation(280, 43, "No live league games running");

            $canvas->drawImage($draw);

            $canvas->setImageFormat("gif");
            $gif->addImage($canvas);
        }

        header("Content-Type: image/gif");
        header("Expires: " . gmdate("D, d M Y H:i:s \G\M\T", time() + (3 * 60 /* 3 minutes */)));
        echo $gif->getImagesBlob();
    }

}