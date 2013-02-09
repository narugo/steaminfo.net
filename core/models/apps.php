<?php

class Apps_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function search($query)
    {
        return self::getApp($query);
    }

    public function getSearchSuggestions($input)
    {
        $cache_key = 'apps_suggestions_for_' . $input;
        $suggestions = $this->memcached->get($cache_key);
        if ($suggestions === FALSE) {
            // TODO: Find a way to get better suggestions
            $statement = $this->db->prepare('
                SELECT *
                FROM app
                WHERE SOUNDEX(`name`) = SOUNDEX(:input)
                LIMIT 0, 5
            ');
            $statement->execute(array(":input" => $this->db->quote($input)));
            $suggestions = $statement->fetchAll();
            $this->memcached->add($cache_key, $suggestions, 3600);
        }
        return $suggestions;
    }

    public function updateAppsInfo($appids)
    {
        $apps = $this->steam->tools->apps->getAppDetails($appids);

        $sql = 'INSERT INTO app (id,
                                 `name`,
                                 header_image_url,
                                 type,
                                 website,
                                 is_win,
                                 is_mac,
                                 is_linux,
                                 recommendations,
                                 detailed_description,
                                 release_date,
                                 legal_notice)
                VALUES (:id,
                        :name,
                        :header_image_url,
                        :type,
                        :website,
                        :is_win,
                        :is_mac,
                        :is_linux,
                        :recommendations,
                        :detailed_description,
                        :release_date,
                        :legal_notice)
                ON DUPLICATE KEY UPDATE id = :id,
                                        `name` = :name,
                                        `header_image_url` = :header_image_url,
                                        `type` = :type,
                                        `website` = :website,
                                        `is_win` = :is_win,
                                        `is_mac` = :is_mac,
                                        `is_linux` = :is_linux,
                                        `recommendations` = :recommendations,
                                        `detailed_description` = :detailed_description,
                                        `release_date` = :release_date,
                                        legal_notice = :legal_notice;';
        $statement = $this->db->prepare($sql);
        foreach ($appids as $app_id) {
            $current_app = $apps->{$app_id};
            if ($current_app->success) {
                $statement->execute(array(
                    ":id" => $current_app->data->steam_appid,
                    ":name" => $current_app->data->name,
                    ":header_image_url" => $current_app->data->header_image,
                    ":type" => $current_app->data->type,
                    ":website" => $current_app->data->website,
                    ":is_win" => $current_app->data->platforms->windows,
                    ":is_mac" => $current_app->data->platforms->mac,
                    ":is_linux" => $current_app->data->platforms->linux,
                    ":recommendations" => $current_app->data->recommendations->total,
                    ":detailed_description" => $current_app->data->detailed_description,
                    ":release_date" => $current_app->data->release_date->date,
                    ":legal_notice" => $current_app->data->legal_notice));
                $statement->closeCursor();
            }
        }
    }

    public function getOwnedApps($community_id)
    {
        $cache_key = 'apps_owned_by_' . $community_id;
        $apps = $this->memcached->get($cache_key);
        if ($apps === FALSE) {
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

        $appids = array();
        foreach ($apps as $app) {
            array_push($apps, $app->appID);
        }
        self::updateAppsInfo($appids);

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

    public function getApp($id)
    {
        $cache_key = 'app_' . $id;
        $app = $this->memcached->get($cache_key);
        if ($app === FALSE) {
            self::updateAppsInfo(array($id));
            $statement = $this->db->prepare('
                SELECT *
                FROM `app`
                WHERE id = :id
             ');
            $statement->execute(array(':id' => $id));
            $app = new App($statement->fetchObject());
            $this->memcached->add($cache_key, $app, 3000);
        }
        return $app;
    }

}

class App
{

    public $id;
    public $name;
    public $header_image_url;
    public $type;
    public $website;
    public $is_win;
    public $is_mac;
    public $is_linux;
    public $recommendations;
    public $detailed_description;
    public $release_date;
    public $legal_notice;

    function __construct($app)
    {
        $this->id = $app->id;
        $this->name = $app->name;
        $this->header_image_url = $app->header_image_url;
        $this->type = $app->type;
        $this->website = $app->website;
        $this->is_win = $app->is_win;
        $this->is_mac = $app->is_mac;
        $this->is_linux = $app->is_linux;
        $this->recommendations = $app->recommendations;
        $this->detailed_description = $app->detailed_description;
        $this->release_date = $app->release_date;
        $this->legal_notice = $app->legal_notice;
    }

    public function getHeaderImageUrl()
    {
        return $this->header_image_url;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIsLinux()
    {
        return $this->is_linux;
    }

    public function getIsMac()
    {
        return $this->is_mac;
    }

    public function getIsWin()
    {
        return $this->is_win;
    }

    public function getLegalNotice()
    {
        return $this->legal_notice;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRecommendations()
    {
        return $this->recommendations;
    }

    public function getReleaseDate()
    {
        return $this->release_date;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getWebsite()
    {
        return $this->website;
    }

}