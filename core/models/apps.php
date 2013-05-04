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

    public function getApp($id)
    {
        $cache_key = 'app_' . $id;
        $app = $this->memcached->get($cache_key);
        if ($app === FALSE) {
            self::updateAppsInfo(array($id));
            $statement = $this->db->prepare('
                SELECT *
                FROM app
                WHERE id = :id
             ');
            $statement->execute(array(':id' => $id));
            $app = $statement->fetchObject();
            $this->memcached->add($cache_key, $app, 3000);
        }
        return $app;
    }

    public function updateAppsInfo($appids)
    {
        $apps = $this->steam->tools->app->getAppDetails($appids);
        foreach ($appids as $app_id) {
            $current_app = $apps->{$app_id};
            if ($current_app->success) {
                $this->db->upsert(
                    'app',
                    array("id" => $current_app->data->steam_appid),
                    array(
                        "name" => $current_app->data->name,
                        "header_image_url" => $current_app->data->header_image,
                        "type" => $current_app->data->type,
                        "website" => $current_app->data->website,
                        "is_win" => $current_app->data->platforms->windows,
                        "is_mac" => $current_app->data->platforms->mac,
                        "is_linux" => $current_app->data->platforms->linux,
                        "recommendations" => $current_app->data->recommendations->total,
                        "detailed_description" => $current_app->data->detailed_description,
                        "release_date" => $current_app->data->release_date->date,
                        "legal_notice" => $current_app->data->legal_notice
                    )
                );
            }
        }
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
                WHERE SOUNDEX(name) = SOUNDEX(:input)
                LIMIT 0, 5
            ');
            $statement->execute(array(":input" => $this->db->quote($input)));
            $suggestions = $statement->fetchAll();
            $this->memcached->add($cache_key, $suggestions, 3600);
        }
        return $suggestions;
    }

}