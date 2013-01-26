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

    public function getUserApps($community_id)
    {
        $status = self::updateUserApps($community_id);

        if ($status === STATUS_SUCCESS) {
            $statement = $this->db->prepare('SELECT id, name, logo_url, used_total, used_last_2_weeks FROM app_owners
            INNER JOIN app ON app_owners.app_id = app.id WHERE user_community_id = :id');
            $statement->execute(array(':id' => $community_id));
            return array(
                'status' => $status,
                'result' => $statement->fetchAll(PDO::FETCH_OBJ)
            );
        } else {
            return array('status' => $status);
        }
    }

    public function updateUserApps($community_id)
    {
        try {
            $apps = $this->steam->communityapi->getAppsForUser($community_id);
        } catch (Exception $e) {
            writeErrorLog($e);
            if ($e instanceof UnauthorizedException) return STATUS_UNAUTHORIZED;
            else if ($e instanceof SteamAPIUnavailableException) return STATUS_API_UNAVAILABLE;
            else return STATUS_UNKNOWN;
        }

        self::updateAppsInfo($apps);

        // Removing old records
        $sql = 'DELETE FROM app_owners WHERE user_community_id= :user_id;';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(":user_id" => $community_id));
        $statement->closeCursor();

        // Adding new
        $sql = "INSERT INTO app_owners (app_id, user_community_id, used_total, used_last_2_weeks) VALUES";
        foreach ($apps as $app) {
            if (empty($app->hoursOnRecord)) $app->hoursOnRecord = 0;
            if (empty($app->hoursLast2Weeks)) $app->hoursLast2Weeks = 0;
            $sql = $sql . "($app->appID, $community_id, $app->hoursOnRecord, $app->hoursLast2Weeks),";
        }
        $sql = substr($sql, 0, -1) . ";";
        $this->db->query($sql);

        return STATUS_SUCCESS;
    }

}