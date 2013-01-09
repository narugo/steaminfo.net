<?php

class Apps_Model extends Model
{

    function __construct()
    {
        parent::__construct();
        $this->steam = new Locomotive();
    }

    public function updateAppsInfo($apps)
    {
        $sql = 'INSERT INTO app (id, name, logo_url) VALUES (:id, :name, :logo_url)
                ON DUPLICATE KEY UPDATE id = :id, name = :name, logo_url = :logo_url;';
        $statement = $this->db->prepare($sql);
        foreach ($apps as $app) {
            $statement->execute(array(
                ":id" => $app->appID,
                ":name" => $app->name,
                ":logo_url" => $app->logo));
            $statement->closeCursor();
        }
    }

    public function getAppsForUser($community_id, $force_update = FALSE)
    {
        if ($force_update === TRUE) {
            $apps = $this->steam->communityapi->getAppsForUser($community_id);
            self::updateAppsInfo($apps);
            self::addAppsForUser($community_id, $apps);
        }
        $statement = $this->db->prepare('SELECT id, name, logo_url, used_total, used_last_2_weeks FROM app_owners
            INNER JOIN app ON app_owners.app_id = app.id WHERE user_community_id = :id');
        $statement->execute(array(':id' => $community_id));
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function addAppsForUser($community_id, $apps)
    {
        // Removing old records
        $sql = 'DELETE FROM app_owners WHERE user_community_id= :user_id;';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(":user_id" => $community_id));
        $statement->closeCursor();

        // Adding new
        $sql = 'INSERT INTO app_owners (app_id, user_community_id, used_total, used_last_2_weeks)
                VALUES (:app_id, :user_community_id, :used_total, :used_last_2_weeks);';
        $statement = $this->db->prepare($sql);
        foreach ($apps as $app) {
            $statement->execute(array(
                ":app_id" => $app->appID,
                ":user_community_id" => $community_id,
                ":used_total" => $app->hoursOnRecord,
                ":used_last_2_weeks" => $app->hoursLast2Weeks));
            $statement->closeCursor();
        }
    }

}