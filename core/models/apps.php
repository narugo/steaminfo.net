<?php

class Apps_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function updateAppsInfo($apps)
    {
        $sql = 'INSERT INTO app (id, `name`, logo_url) VALUES (:id, :name, :logo_url)
                ON DUPLICATE KEY UPDATE id = :id, `name` = :name, logo_url = :logo_url;';
        $statement = $this->db->prepare($sql);
        foreach ($apps as $app) {
            $statement->execute(array(
                ":id" => $app->appID,
                ":name" => $app->name,
                ":logo_url" => $app->logo));
            $statement->closeCursor();
        }
    }

    public function getOwnedApps($community_id)
    {
        $cache_key = 'apps_owned_by_' . $community_id;
        $apps = $this->memcached->get($cache_key);
        if ($apps == FALSE) {
            self::updateOwnedApps($community_id);
            $statement = $this->db->prepare('SELECT id, `name`, logo_url, used_total, used_last_2_weeks FROM app_owners
            INNER JOIN app ON app_owners.app_id = app.id WHERE user_community_id = :id');
            $statement->execute(array(':id' => $community_id));
            $apps = $statement->fetchAll(PDO::FETCH_OBJ);
            $this->memcached->add($cache_key, $apps, 3600);
        }
        return $apps;
    }

    public function updateOwnedApps($community_id)
    {
        $apps = $this->steam->communityapi->getOwnedApps($community_id);

        self::updateAppsInfo($apps);

        // Removing old records
        $sql = 'DELETE FROM app_owners WHERE user_community_id= :user_id;';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(":user_id" => $community_id));
        $statement->closeCursor();

        // Adding new
        $sql = "INSERT INTO app_owners (app_id, user_community_id, used_total, used_last_2_weeks) VALUES";
        foreach ($apps as $app) {
            if (empty($app->hoursOnRecord)) {
                $app->hoursOnRecord = 0;
            } else {
                $app->hoursOnRecord = str_replace(',', '', $app->hoursOnRecord);
            }
            if (empty($app->hoursLast2Weeks)) {
                $app->hoursLast2Weeks = 0;
            } else {
                $app->hoursOnRecord = str_replace(',', '', $app->hoursLast2Weeks);
            }
            $sql = $sql . "($app->appID, $community_id, $app->hoursOnRecord, $app->hoursLast2Weeks),";
        }
        $sql = substr($sql, 0, -1) . ";";
        $this->db->query($sql);
    }

}