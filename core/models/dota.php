<?php

class Dota_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function updateHeroes()
    {
        $heroes = $this->steam->IEconDOTA2_570->GetHeroes();
        foreach ($heroes as $hero) {
            $this->db->upsert(
                'dota_hero',
                array("id" => $hero->id),
                array("name" => $hero->name)
            );
        }
    }

    public function getMatchDetails($match_id)
    {
        $match_cache_key = 'dota_match_' . $match_id;
        $match_players_key = 'dota_match_' . $match_id . '_players';
        $match = $this->memcached->get($match_cache_key);
        $players = $this->memcached->get($match_players_key);
        if (($match === FALSE) OR ($players === FALSE)) {
            $match = self::getMatchFromDB($match_id);
            $players = self::getMatchPlayers($match_id);
            if (!$match OR !$players) {
                $response = $this->steam->IDOTA2Match_570->GetMatchDetails($match_id);
                self::addMatch($response->result);
                $match = self::getMatchFromDB($match_id);
                $players = self::getMatchPlayers($match_id);
            }
            $this->memcached->add($match_cache_key, $match, 3000);
            $this->memcached->add($match_players_key, $players, 3000);
        }
        return array(
            'match' => $match,
            'players' => $players
        );
    }

    private function getMatchFromDB($match_id)
    {
        $sql = 'SELECT dota_match.*,
                    radiant_team.name AS radiant_name, radiant_team.logo AS radiant_logo,
                    dire_team.name AS dire_name, dire_team.logo AS dire_logo
                FROM dota_match
                LEFT JOIN dota_team AS radiant_team ON dota_match.radiant_team_id = radiant_team.id
                LEFT JOIN dota_team AS dire_team ON dota_match.dire_team_id = dire_team.id
                WHERE dota_match.id = :match_id';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(':match_id' => $match_id));
        if ($statement->rowCount() < 1) return FALSE;
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        return $result[0];
    }

    private function getMatchPlayers($match_id)
    {
        $sql = 'SELECT dota_match_player.*, nickname, dota_hero.name AS hero_name, dota_hero.display_name AS hero_display_name
                FROM dota_match_player
                LEFT JOIN steam_user ON steam_user.community_id = dota_match_player.account_id
                LEFT JOIN dota_hero ON dota_hero.id = dota_match_player.hero_id
                WHERE match_id = :match_id';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(':match_id' => $match_id));
        if ($statement->rowCount() < 1) return FALSE;
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    private function addMatch($match)
    {
        // TODO: Fix (adding existing teams)
        $sql = 'INSERT INTO dota_team (id, name, logo) VALUES (:team_id, :name, :logo);';
        $statement = $this->db->prepare($sql);

        if (!empty($match->radiant_team_id)) {
            if (!empty($match->radiant_logo)) $match->radiant_logo = self::getTeamLogo($match->radiant_logo);
            $statement->execute(array(
                ":team_id" => $match->radiant_team_id,
                ":name" => $match->radiant_name,
                ":logo" => $match->radiant_logo));
            $statement->closeCursor();
        }

        if (!empty($match->dire_team_id)) {
            if (!empty($match->dire_logo)) $match->dire_logo = self::getTeamLogo($match->dire_logo);
            $statement->execute(array(
                ":team_id" => $match->dire_team_id,
                ":name" => $match->dire_name,
                ":logo" => $match->dire_logo));
            $statement->closeCursor();
        }

        // TODO: Fix (adding existing leagues)
        $sql = 'INSERT INTO dota_league (id) VALUES (:league_id);
                INSERT INTO dota_match (id, start_time, season, radiant_win, duration, tower_status_radiant,
                                        tower_status_dire, barracks_status_radiant, barracks_status_dire, cluster,
                                        first_blood_time, lobby_type, human_players, league_id, positive_votes,
                                        negative_votes, game_mode, radiant_team_id, dire_team_id)
                VALUES (:id, :start_time, :season, :radiant_win, :duration, :tower_status_radiant,
                        :tower_status_dire, :barracks_status_radiant, :barracks_status_dire, :cluster,
                        :first_blood_time, :lobby_type, :human_players, :league_id, :positive_votes,
                        :negative_votes, :game_mode, :radiant_team_id, :dire_team_id);';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(
            ":id" => $match->match_id,
            ":start_time" => $match->starttime,
            ":season" => $match->season,
            ":radiant_win" => $match->radiant_win,
            ":duration" => $match->duration,
            ":tower_status_radiant" => $match->tower_status_radiant,
            ":tower_status_dire" => $match->tower_status_dire,
            ":barracks_status_radiant" => $match->barracks_status_radiant,
            ":barracks_status_dire" => $match->barracks_status_dire,
            ":cluster" => $match->cluster,
            ":first_blood_time" => $match->first_blood_time,
            ":lobby_type" => $match->lobby_type,
            ":human_players" => $match->human_players,
            ":league_id" => $match->leagueid,
            ":positive_votes" => $match->positive_votes,
            ":negative_votes" => $match->negative_votes,
            ":game_mode" => $match->game_mode,
            ":radiant_team_id" => $match->radiant_team_id,
            ":dire_team_id" => $match->dire_team_id));
        $statement->closeCursor();

        self::addMatchPlayers($match->match_id, $match->players);
    }

    private function getTeamLogo($logo_id)
    {
        $response = $this->steam->ISteamRemoteStorage->GetUGCFileDetails($logo_id, 570);
        $path = PATH_TO_ASSETS . 'img/dota/' . $response->data->filename . '.png';
        $fp = fopen($path, 'w');
        $ch = curl_init($response->data->url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $response->data->filename . '.png';
    }

    private function addMatchPlayers($match_id, $players)
    {
        // Removing old records
        $sql = 'DELETE FROM dota_match_player WHERE match_id = :match_id;';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(":match_id" => $match_id));
        $statement->closeCursor();

        $ids = array();
        foreach ($players as $player) {
            if ($player->account_id == 4294967295) {
                $player->account_id = NULL;
            } else {
                $odd_id = $player->account_id % 2;
                $temp = floor($player->account_id / 2);
                $steam_id = '0:' . $odd_id . ':' . $temp;
                $player->account_id = $this->steam->tools->users->steamIdToCommunityId($steam_id);
                array_push($ids, $player->account_id);
            }
        }
        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $users_model->updateSummaries($ids);

        $sql = 'INSERT INTO dota_match_player (account_id, match_id, player_slot, hero_id,
                                               item_0, item_1, item_2, item_3, item_4, item_5,
                                               kills, deaths, assists, leaver_status, gold, last_hits, denies,
                                               gold_per_min, xp_per_min, gold_spent, hero_damage, tower_damage,
                                               hero_healing, level)
                VALUES (:account_id, :match_id, :player_slot, :hero_id,
                        :item_0, :item_1, :item_2, :item_3, :item_4, :item_5,
                        :kills, :deaths, :assists, :leaver_status, :gold, :last_hits, :denies,
                        :gold_per_min, :xp_per_min, :gold_spent, :hero_damage, :tower_damage,
                        :hero_healing, :LEVEL);';
        $statement = $this->db->prepare($sql);
        foreach ($players as $player) {
            $statement->execute(array(
                ":account_id" => $player->account_id,
                ":match_id" => $match_id,
                ":player_slot" => $player->player_slot,
                ":hero_id" => $player->hero_id,
                ":item_0" => $player->item_0,
                ":item_1" => $player->item_1,
                ":item_2" => $player->item_2,
                ":item_3" => $player->item_3,
                ":item_4" => $player->item_4,
                ":item_5" => $player->item_5,
                ":kills" => $player->kills,
                ":deaths" => $player->deaths,
                ":assists" => $player->assists,
                ":leaver_status" => $player->leaver_status,
                ":gold" => $player->gold,
                ":last_hits" => $player->last_hits,
                ":denies" => $player->denies,
                ":last_hits" => $player->last_hits,
                ":gold_per_min" => $player->gold_per_min,
                ":xp_per_min" => $player->xp_per_min,
                ":gold_spent" => $player->gold_spent,
                ":gold_spent" => $player->last_hits,
                ":hero_damage" => $player->hero_damage,
                ":tower_damage" => $player->tower_damage,
                ":hero_healing" => $player->hero_healing,
                ":level" => $player->level));
            $statement->closeCursor();
        }
    }

    public function getTeamDetails($team_id)
    {
        $cache_key = 'dota_team_' . $team_id;
        $team = $this->memcached->get($cache_key);
        if ($team === FALSE) {
            $response = $this->steam->IDOTA2Match_570->GetTeamInfoByTeamID($team_id, 1);
            self::updateTeams($response->teams);
            $team = self::getTeam($team_id);
            $this->memcached->add($cache_key, $team, 3000);
        }
        return $team;
    }

    private function updateTeams($teams)
    {
        $ids = array();
        foreach ($teams as $team) {
            $team->admin_account_id = self::convertUserID($team->admin_account_id);
            array_push($ids, $team->admin_account_id);
            if (!empty($team->player_0_account_id)) {
                $team->player_0_account_id = self::convertUserID($team->player_0_account_id);
                array_push($ids, self::convertUserID($team->player_0_account_id));
            }
            if (!empty($team->player_1_account_id)) {
                $team->player_1_account_id = self::convertUserID($team->player_1_account_id);
                array_push($ids, self::convertUserID($team->player_1_account_id));
            }
            if (!empty($team->player_2_account_id)) {
                $team->player_2_account_id = self::convertUserID($team->player_2_account_id);
                array_push($ids, self::convertUserID($team->player_2_account_id));
            }
            if (!empty($team->player_3_account_id)) {
                $team->player_3_account_id = self::convertUserID($team->player_3_account_id);
                array_push($ids, self::convertUserID($team->player_3_account_id));
            }
            if (!empty($team->player_4_account_id)) {
                $team->player_4_account_id = self::convertUserID($team->player_4_account_id);
                array_push($ids, self::convertUserID($team->player_4_account_id));
            }
        }

        require_once PATH_TO_MODELS . 'users.php';
        $users_model = new Users_Model();
        $users_model->updateSummaries(array_unique($ids));

        foreach ($teams as $team) {
            $team->logo = self::getTeamLogo($team->logo);
            $this->db->upsert(
                'dota_team',
                array("id" => $team->team_id),
                array(
                    "name" => $team->name,
                    "tag" => $team->tag,
                    "creation_time" => $team->time_created,
                    "rating" => $team->rating,
                    "logo" => $team->logo,
                    "logo_sponsor" => $team->logo_sponsor,
                    "country_code" => $team->country_code,
                    "url" => $team->url,
                    "games_played_with_current_roster" => $team->games_played_with_current_roster,
                    "player_0" => $team->player_0_account_id,
                    "player_1" => $team->player_1_account_id,
                    "player_2" => $team->player_2_account_id,
                    "player_3" => $team->player_3_account_id,
                    "player_4" => $team->player_4_account_id,
                    "admin_account" => $team->admin_account_id
                )
            );
        }
    }

    private function convertUserID($id)
    {
        $odd_id = $id % 2;
        $temp = floor($id / 2);
        $steam_id = '0:' . $odd_id . ':' . $temp;
        return $this->steam->tools->users->steamIdToCommunityId($steam_id);
    }

    private function getTeam($team_id)
    {
        $statement = $this->db->prepare('
                SELECT *
                FROM dota_team
                WHERE id = :id
             ');
        $statement->execute(array(':id' => $team_id));
        return $statement->fetchObject();
    }

    public function getLiveLeagueMatches()
    {
        $cache_key = 'dota_live';
        $games = $this->memcached->get($cache_key);
        if ($games === FALSE) {
            $response = $this->steam->IDOTA2Match_570->GetLiveLeagueGames();
            $games = $response->games;
            $this->memcached->set($cache_key, $games, 240);
        }
        return $games;
    }

    public function getLeagueListing()
    {
        $cache_key = 'dota_league_listing';
        $leagues = $this->memcached->get($cache_key);
        if ($leagues === FALSE) {
            self::updateLeagues();
            $statement = $this->db->query('SELECT * FROM dota_league ORDER BY id');
            if ($statement->rowCount() < 1) return FALSE;
            $leagues = $statement->fetchAll(PDO::FETCH_OBJ);
            // $this->memcached->set($cache_key, $leagues, 3600);
        }
        return $leagues;
    }

    private function updateLeagues()
    {
        $response = $this->steam->IDOTA2Match_570->GetLeagueListing();
        $this->db->beginTransaction();
        $statements = $this->db->getUpsertStatements(
            dota_league,
            array('id'),
            array('name', 'description', 'tournament_url')
        );
        foreach ($response->result->leagues as $league) {
            $params = array(
                'id' => $league->leagueid,
                'name' => $league->name,
                'description' => $league->description,
                'tournament_url' => $league->tournament_url
            );
            $statements['update']->execute($params);
            $statements['update']->closeCursor();
            $statements['insert']->execute($params);
            $statements['insert']->closeCursor();
        }
        $this->db->commit();
    }

}