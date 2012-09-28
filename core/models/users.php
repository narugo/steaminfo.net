<?php

/**
 * Database interface
 */
class Users_Model extends Model {

    function __construct() {
        parent::__construct();
        require "core/libs/steamapi.php";
        $this->steam_api = new SteamAPI();
    }

    public function getProfile($community_id, $no_update = FALSE) {
        $update_status = NULL;
        if ($no_update === FALSE) {
            try {
                $results = $this->steam_api->GetPlayerSummaries(array($community_id));
                $profile = $results[0];
                self::updateUserInfo($profile);

                // Trying to get more info from Community API
                $additional_info = self::getAdditionalProfileInfo($community_id);
                if ($additional_info === FALSE) {
                    $update_status = "incomplete";
                } else {
                    self::updateAdditionalProfileInfo($additional_info);
                    $update_status = "success";
                }
            } catch (SteamAPIUnavailableException $e) {
                $update_status = "fail";
            }
        } else {
            $update_status = "no_update";
        }
        $statement = $this->db->prepare('SELECT * FROM users WHERE community_id=:id');
        $statement->execute(array(':id' => $community_id));
        // TODO: Check if found
        return array(
            'profile' => $statement->fetchObject(),
            'update_status' => $update_status);
    }

    public function getApps($community_id, $no_update = FALSE) {
        $update_status = NULL;
        if ($no_update === FALSE) {
            try {
                $apps = $this->steam_api->getAppsForUser($community_id);
                self::addAppsForUser($community_id, $apps);
                $update_status = "success";
            } catch (Exception $e) {
                $update_status = "fail";
            }
        } else {
            $update_status = "no_update";
        }
        $statement = $this->db->prepare('SELECT id, name, logo_url, used_total, used_last_2_weeks FROM app_owners
                INNER JOIN apps ON app_owners.app_id = apps.id
                WHERE user_community_id = :id');
        $statement->execute(array(':id' => $community_id));
        return array(
            'apps' => $statement->fetchAll(PDO::FETCH_OBJ),
            'update_status' => $update_status);
    }

    public function getFriends($community_id, $no_update = FALSE) {
        $update_status = NULL;
        if ($no_update === FALSE) {
            try {
                $friends = $this->steam_api->GetFriendList($community_id);
                self::updateFriends($community_id, $friends);
                $update_status = "success";
            } catch (Exception $e) {
                if ($e instanceof PrivateProfileException) {
                    $update_status = "api_unavailable";
                } else {
                    $update_status = "fail";
                }
            }
        } else {
            $update_status = "no_update";
        }
        $statement = $this->db->prepare('SELECT community_id, nickname, avatar_url, tag, since FROM friends
                INNER JOIN users ON friends.user_community_id2 = users.community_id
                WHERE user_community_id1 = :id');
        $statement->execute(array(':id' => $community_id));
        return array(
            'friends' => $statement->fetchAll(PDO::FETCH_OBJ),
            'update_status' => $update_status);
    }

    public function getGroups($community_id, $no_update = FALSE) {
        if ($no_update === FALSE) {
            // TODO: Get groups from Steam
        }
        $statement = $this->db->prepare('SELECT id, name, url, avatar_url FROM group_members
                INNER JOIN groups ON group_members.group_id = groups.id
                WHERE user_community_id = :id');
        $statement->execute(array(':id' => $community_id));
        $groups = $statement->fetchAll(PDO::FETCH_OBJ);
        if (empty($groups)) return NULL;
        return $groups;
    }

    public function getBadges($community_id)
    {
        if (self::is_valid_id($community_id, "communityid") !== TRUE) throw new WrongIDException($community_id);
        require_once $_SERVER['DOCUMENT_ROOT']."/core/simple_html_dom.php";
        $url = 'http://steamcommunity.com/profiles/'.$community_id;
        $html = file_get_html($url);
        $badges_html = '';
        foreach($html->find('img.profile_badge_icon') as $element)
        {
            $badges_html .= $element;
        }
        return $badges_html;
    }

    public function setTag($community_id, $tag) {
        // TODO: Modify function so it can get multiple IDs
        $statement = $this->db->prepare('UPDATE users SET tag= :tag WHERE community_id= :id');
        return $statement->execute(array(':tag' => $tag, ':id' => $community_id));
    }

    /**
     * Database info updaters
     */

    private function updateUserInfo($profile) {
        $sql = "INSERT INTO users (
                community_id,
                nickname,
                avatar_url,
                time_created,
                real_name,
                location_country_code,
                location_state_code,
                location_city_id,
                last_login_time,
                status,
                current_game_server_ip,
                current_game_name,
                current_game_id,
                primary_group_id)
            VALUES (
                :community_id,
                :nickname,
                :avatar_url,
                :time_created,
                :real_name,
                :location_country_code,
                :location_state_code,
                :location_city_id,
                :last_login_time,
                :status,
                :current_game_server_ip,
                :current_game_name,
                :current_game_id,
                :primary_group_id)
            ON DUPLICATE KEY UPDATE
                community_id = :community_id,
                nickname = :nickname,
                avatar_url = :avatar_url,
                time_created = :time_created,
                real_name = :real_name,
                location_country_code = :location_country_code,
                location_state_code = :location_state_code,
                location_city_id = :location_city_id,
                last_login_time = :last_login_time,
                status = :status,
                current_game_server_ip = :current_game_server_ip,
                current_game_name = :current_game_name,
                current_game_id = :current_game_id,
                primary_group_id = :primary_group_id";
        $statement = $this->db->prepare($sql);
        $statement->execute(array(
            ":community_id" => $profile->steamid,
            ":nickname" => $profile->personaname,
            ":avatar_url" => $profile->avatar,
            ":time_created" => $profile->timecreated,
            ":real_name" => $profile->realname,
            ":location_country_code" => $profile->loccountrycode,
            ":location_state_code" => $profile->locstatecode,
            ":location_city_id" => $profile->loccityid,
            ":last_login_time" => $profile->lastlogoff,
            ":status" => $profile->personastate,
            ":current_game_server_ip" => $profile->gameserverip,
            ":current_game_name" => $profile->gameextrainfo,
            ":current_game_id" => $profile->gameid,
            ":primary_group_id" => $profile->primaryclanid));
    }

    private function getAdditionalProfileInfo($community_id) {
        $url = 'http://steamcommunity.com/profiles/'.$community_id.'/?xml=1&l=english';
        $contents = file_get_contents($url);
        if ($contents === FALSE) return false;
        $additional_info = new SimpleXMLElement($contents);
        if (isset($additional_info->error)) return false;
        return $additional_info;
    }

    private function updateAdditionalProfileInfo($profile)
    {
        $sql = "INSERT INTO users(
                community_id,
                is_limited_account,
                is_vac_banned,
                trade_ban_state)
            VALUES (
                :community_id,
                :is_limited_account,
                :is_vac_banned,
                :trade_ban_state)
            ON DUPLICATE KEY UPDATE
                community_id = :community_id,
                is_limited_account = :is_limited_account,
                is_vac_banned = :is_vac_banned,
                trade_ban_state = :trade_ban_state";
        $statement = $this->db->prepare($sql);
        $statement->execute(array(
            ":community_id" => $profile->steamID64,
            ":is_limited_account" => $profile->isLimitedAccount,
            ":is_vac_banned" => $profile->vacBanned,
            ":trade_ban_state" => $profile->tradeBanState));

        if(isset($profile->groups))
        {
            // Removing old records
            $sql = 'DELETE FROM group_members WHERE user_community_id= :user_community_id;';
            $statement = $this->db->prepare($sql);
            $statement->execute(array(":user_community_id" => $profile->steamID64));
            $statement->closeCursor();

            $sql = 'INSERT IGNORE INTO groups (id) VALUES (:group_id);
                    INSERT INTO group_members (group_id, user_community_id)
                    VALUES (:group_id, :user_community_id)
                    ON DUPLICATE KEY UPDATE group_id = :group_id, user_community_id = :user_community_id;';
            $statement = $this->db->prepare($sql);
            foreach ($profile->groups->group as $group)
            {
                $statement->execute(array(
                    ":group_id" => $group->groupID64,
                    ":user_community_id" => $profile->steamID64));
                $statement->closeCursor();
            }
        }
    }

    private function updateFriends($community_id, $friends_list)
    {
        // Removing old records
        $sql = 'DELETE FROM friends WHERE user_community_id1= :user_id;';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(":user_id" => $community_id));
        $statement->closeCursor();

        // Adding new
        $sql = 'INSERT IGNORE INTO users (community_id) VALUES (:friend_id);
                INSERT INTO friends (user_community_id1, user_community_id2, since)
                VALUES (:user_id, :friend_id, :friend_since);';
        $statement = $this->db->prepare($sql);
        foreach ($friends_list as $friend)
        {
            $statement->execute(array(
                ":user_id" => $community_id,
                ":friend_id" => $friend->steamid,
                ":friend_since" => date(DATE_W3C, $friend->friend_since)));
            // TODO: Fix: friend_since variable can be "0"
            $statement->closeCursor();
        }
    }



    private function getMultipleUsersInfo($ids) {
        $url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.STEAM_API_KEY.'&steamids=';
        global $db;
        $result = array();
        foreach (array_chunk($ids, 100) as $chunk) {
            $string = implode(",", $chunk);
            $contents = @file_get_contents($url.$string);
            if ($contents === FALSE) throw new SteamAPIUnavailableException($contents);
            $json = json_decode($contents);
            $profiles = $json->response->players;
            foreach ($profiles as $profile) {
                // TODO: Optimize (right now there are 2 DB queries per profile)
                $db->updateUserInfo($profile);
                array_push($result, $db->getUserInfo($profile->steamid));
            }
        }
        return $result;
    }


    // TODO: Move this method to another model
    private function updateAppsInfo($apps)
    {
        $sql = 'INSERT INTO apps (id, name, logo_url)
                    VALUES (:id, :name, :logo_url)
                    ON DUPLICATE KEY UPDATE id = :id, name = :name, logo_url = :logo_url;';
        $statement = $this->db->prepare($sql);
        foreach ($apps as $app)
        {
            $statement->execute(array(
                ":id" => $app->appID,
                ":name" => $app->name,
                ":logo_url" => $app->logo));
            $statement->closeCursor();
        }
    }

    private function addAppsForUser($community_id, $apps)
    {
        // Removing old records
        $sql = 'DELETE FROM app_owners WHERE user_community_id= :user_id;';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(":user_id" => $community_id));
        $statement->closeCursor();

        self::updateAppsInfo($apps);

        // Adding new
        $sql = 'INSERT INTO app_owners (app_id, user_community_id, used_total, used_last_2_weeks)
                VALUES (:app_id, :user_community_id, :used_total, :used_last_2_weeks);';
        $statement = $this->db->prepare($sql);
        foreach ($apps as $app)
        {
            $statement->execute(array(
                ":app_id" => $app->appID,
                ":user_community_id" => $community_id,
                ":used_total" => $app->hoursOnRecord,
                ":used_last_2_weeks" => $app->hoursLast2Weeks));
            $statement->closeCursor();
        }
    }


}