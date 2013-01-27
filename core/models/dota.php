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
                $response = $this->steam->webapi->GetMatchDetails($match_id);
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
        $heroes = $this->steam->webapi->GetHeroes();
        $sql = 'INSERT INTO dota_hero (id, NAME) VALUES (:id, :NAME)
                ON DUPLICATE KEY UPDATE id = :id, NAME = :NAME;';
        $statement = $this->db->prepare($sql);
        foreach ($heroes as $hero) {
            $statement->execute(array(
                ":id" => $hero->id,
                ":NAME" => $hero->name));
            $statement->closeCursor();
        }
    }

    private function addMatch($match)
    {
        $sql = 'INSERT INTO dota_match (id, start_time, season, radiant_win, duration, tower_status_radiant,
                                        tower_status_dire, barracks_status_radiant, barracks_status_dire, cluster,
                                        first_blood_time, lobby_type, human_players, league_id, positive_votes,
                                        negative_votes, game_mode)
                VALUES (:id, :start_time, :season, :radiant_win, :duration, :tower_status_radiant,
                        :tower_status_dire, :barracks_status_radiant, :barracks_status_dire, :cluster,
                        :first_blood_time, :lobby_type, :human_players, :league_id, :positive_votes,
                        :negative_votes, :game_mode);';
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
            ":game_mode" => $match->game_mode));
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

        $sql = 'INSERT INTO dota_match_player (account_id, match_id, player_slot, hero_id,
                                               item_0, item_1, item_2, item_3, item_4, item_5,
                                               kills, deaths, assists, leaver_status, gold, last_hits, denies,
                                               gold_per_min, xp_per_min, gold_spent, hero_damage, tower_damage,
                                               hero_healing, LEVEL)
                VALUES (:account_id, :match_id, :player_slot, :hero_id,
                        :item_0, :item_1, :item_2, :item_3, :item_4, :item_5,
                        :kills, :deaths, :assists, :leaver_status, :gold, :last_hits, :denies,
                        :gold_per_min, :xp_per_min, :gold_spent, :hero_damage, :tower_damage,
                        :hero_healing, :LEVEL);';
        $statement = $this->db->prepare($sql);
        foreach ($players as $player) {
            if ($player->account_id == 4294967295)
                $player->account_id = NULL;
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
                ":LEVEL" => $player->level));
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
        $statement = $this->db->prepare('SELECT * FROM dota_match_player WHERE match_id = :match_id');
        $statement->execute(array(':match_id' => $match_id));
        if ($statement->rowCount() < 1) return FALSE;
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

}