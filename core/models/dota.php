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
        try {
            $match = $this->steam->webapi->GetMatchDetails($match_id);
            return array(
                'status' => STATUS_SUCCESS,
                'result' => $match
            );
        } catch (Exception $e) {
            if ($e instanceof SteamAPIUnavailableException) {
                return array('status' => STATUS_API_UNAVAILABLE);
            } else if ($e instanceof UnauthorizedException) {
                return array('status' => STATUS_UNAUTHORIZED);
            }
        }
    }

    public function updateHeroes()
    {
        $heroes = $this->steam->webapi->GetHeroes();
        $sql = 'INSERT INTO dota_hero (id, name) VALUES (:id, :name)
                ON DUPLICATE KEY UPDATE id = :id, name = :name;';
        $statement = $this->db->prepare($sql);
        foreach ($heroes as $hero) {
            $statement->execute(array(
                ":id" => $hero->id,
                ":name" => $hero->name));
            $statement->closeCursor();
        }
    }

}