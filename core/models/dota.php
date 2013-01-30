<?php

class Dota_Model extends Model
{

    function __construct()
    {
        parent::__construct();
        $this->steam = new Locomotive();
    }

    public function getMatchDetails($match_id)
    {
        $match = self::getMatch($match_id);
        $players = self::getPlayers($match->id);

        if (!$match OR !$players) {
            try {
                $response = $this->steam->IDOTA2Match_570->GetMatchDetails($match_id);
            } catch (Exception $e) {
                if ($e instanceof SteamAPIUnavailableException) {
                    return array('status' => STATUS_API_UNAVAILABLE);
                } else if ($e instanceof UnauthorizedException) {
                    return array('status' => STATUS_UNAUTHORIZED);
                }
            }
            self::addMatch($response);
            $match = self::getMatch($match_id);
            $players = self::getPlayers($match->id);
        }

        return array(
            'status' => STATUS_SUCCESS,
            'match' => $match,
            'players' => $players
        );
    }

    public function updateHeroes()
    {
        $heroes = $this->steam->IEconDOTA2_570->GetHeroes();
        $sql = 'INSERT INTO dota_hero (id, `name`) VALUES (:id, :name)
                ON DUPLICATE KEY UPDATE id = :id, `name` = :name;';
        $statement = $this->db->prepare($sql);
        foreach ($heroes as $hero) {
            $statement->execute(array(
                ":id" => $hero->id,
                ":name" => $hero->name));
            $statement->closeCursor();
        }
    }

    private function addMatch($match)
    {
        if (!empty($match->radiant_logo)) $match->radiant_logo = self::getTeamLogo($match->radiant_logo);
        if (!empty($match->dire_logo)) $match->dire_logo = self::getTeamLogo($match->dire_logo);

        $sql = 'INSERT INTO dota_match (id, start_time, season, radiant_win, duration, tower_status_radiant,
                                        tower_status_dire, barracks_status_radiant, barracks_status_dire, cluster,
                                        first_blood_time, lobby_type, human_players, league_id, positive_votes,
                                        negative_votes, game_mode,
                                        radiant_name, radiant_logo, radiant_team_complete,
                                        dire_name, dire_logo, dire_team_complete)
                VALUES (:id, :start_time, :season, :radiant_win, :duration, :tower_status_radiant,
                        :tower_status_dire, :barracks_status_radiant, :barracks_status_dire, :cluster,
                        :first_blood_time, :lobby_type, :human_players, :league_id, :positive_votes,
                        :negative_votes, :game_mode,
                        :radiant_name, :radiant_logo, :radiant_team_complete,
                        :dire_name, :dire_logo, :dire_team_complete);';
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
            ":radiant_name" => $match->radiant_name,
            ":radiant_logo" => $match->radiant_logo,
            ":radiant_team_complete" => $match->radiant_team_complete,
            ":dire_name" => $match->dire_name,
            ":dire_logo" => $match->dire_logo,
            ":dire_team_complete" => $match->dire_team_complete));
        $statement->closeCursor();

        self::addPlayers($match->players, $match->match_id);
    }

    private function addPlayers($players, $match_id)
    {
        // Removing old records
        $sql = 'DELETE FROM dota_match_player WHERE match_id= :match_id;';
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
        $users_model = getModel('users');
        $users_model->updateSummaries($ids);

        $sql = 'INSERT INTO dota_match_player (account_id, match_id, player_slot, hero_id,
                                               item_0, item_1, item_2, item_3, item_4, item_5,
                                               kills, deaths, assists, leaver_status, gold, last_hits, denies,
                                               gold_per_min, xp_per_min, gold_spent, hero_damage, tower_damage,
                                               hero_healing, `level`)
                VALUES (:account_id, :match_id, :player_slot, :hero_id,
                        :item_0, :item_1, :item_2, :item_3, :item_4, :item_5,
                        :kills, :deaths, :assists, :leaver_status, :gold, :last_hits, :denies,
                        :gold_per_min, :xp_per_min, :gold_spent, :hero_damage, :tower_damage,
                        :hero_healing, :level);';
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

    private function getMatch($match_id)
    {
        $statement = $this->db->prepare('SELECT * FROM dota_match WHERE id = :match_id');
        $statement->execute(array(':match_id' => $match_id));
        if ($statement->rowCount() < 1) return FALSE;
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        return $result[0];
    }

    private function getPlayers($match_id)
    {
        $sql = 'SELECT dota_match_player.*, nickname, dota_hero.name AS hero_name, dota_hero.display_name AS hero_display_name
                FROM dota_match_player
                LEFT JOIN `user` ON `user`.community_id = dota_match_player.account_id
                LEFT JOIN dota_hero ON dota_hero.id = dota_match_player.hero_id
                WHERE match_id = :match_id';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(':match_id' => $match_id));
        if ($statement->rowCount() < 1) return FALSE;
        return $statement->fetchAll(PDO::FETCH_OBJ);
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

}