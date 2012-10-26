<?php

/**
 * Database interface
 */
class Users_Model extends Model {

    function __construct() {
        parent::__construct();
        $this->steam = new Locomotive();
    }

    /**
     * @param $query Steam ID, Community ID, or Vanity URL
     * @return array Array of results
     * Result is empty if nothing was found
     */
    public function search($query) {
        $query_type = $this->steam->tools->users->getTypeOfQuery($query);
        switch ($query_type) {
            case TYPE_COMMUNITY_ID:
                $result = $this->steam->webapi->GetPlayerSummaries(array($query));
                break;
            case TYPE_STEAM_ID:
                // TODO: Search in database in case vanity url or something else other than community/steam id has been requested
                $community_id = $this->steam->tools->users->convertToCommunityID($query);
                $result = $this->steam->webapi->GetPlayerSummaries(array($community_id));
                break;
            case TYPE_VANITY:
                $community_id = $this->steam->webapi->ResolveVanityURL($query);
                $result = $this->steam->webapi->GetPlayerSummaries(array($community_id));
                break;
            default:
                // Error (unknown type of query)
                $result = array();
        }
        return $result;
    }

    public function getProfile($community_id, $no_update = FALSE) {
        if (! $this->steam->tools->users->validateUserId($community_id, TYPE_COMMUNITY_ID)) {
            throw new WrongIDException();
        }
        $update_status = NULL;
        if ($no_update === FALSE) {
            try {
                $results = $this->steam->webapi->GetPlayerSummaries(array($community_id));
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
            'profile' => new User($statement->fetchObject()),
            'update_status' => $update_status);
    }

    public function getApps($community_id, $no_update = FALSE) {
        if (! $this->steam->tools->users->validateUserId($community_id, TYPE_COMMUNITY_ID)) {
            throw new WrongIDException();
        }
        $update_status = NULL;
        if ($no_update === FALSE) {
            try {
                $apps = $this->steam->communityapi->getAppsForUser($community_id);
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
        if (! $this->steam->tools->users->validateUserId($community_id, TYPE_COMMUNITY_ID)) {
            throw new WrongIDException();
        }
        $update_status = NULL;
        if ($no_update === FALSE) {
            try {
                $friends = $this->steam->webapi->GetFriendList($community_id);
                self::updateFriendsList($community_id, $friends);
                // Updating friend's profiles
                $friend_ids = array();
                foreach ($friends as $friend) {
                    array_push($friend_ids, $friend->steamid);
                }
                $friend_profiles = $this->steam->webapi->GetPlayerSummaries($friend_ids);
                foreach ($friend_profiles as $friend_profile) {
                    self::updateUserInfo($friend_profile);
                }
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
        if (! $this->steam->tools->users->validateUserId($community_id, TYPE_COMMUNITY_ID)) {
            throw new WrongIDException();
        }
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

    public function setTag($community_id, $tag) {
        if (! $this->steam->tools->users->validateUserId($community_id, TYPE_COMMUNITY_ID)) {
            throw new WrongIDException();
        }
        // TODO: Modify function so it can get multiple IDs
        $statement = $this->db->prepare('UPDATE users SET tag= :tag WHERE community_id= :id');
        return $statement->execute(array(':tag' => $tag, ':id' => $community_id));
    }

    /**
     * Database info updaters
     */

    private function updateUserInfo($profile) {
        // Adding primary group into 'groups' table
        if (isset($profile->primaryclanid)) {
            $sql = 'INSERT IGNORE INTO groups (id) VALUES (:group_id)';
            $statement = $this->db->prepare($sql);
            $statement->execute(array(":group_id" => $profile->primaryclanid));
            $statement->closeCursor();
        }

        $sql = "INSERT INTO users (
                community_id,
                nickname,
                avatar_url,
                creation_time,
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
                :creation_time,
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
                creation_time = :creation_time,
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
            ":creation_time" => $profile->timecreated,
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
        $contents = @file_get_contents($url);
        if ($contents === FALSE) return FALSE;
        try {
            $additional_info = new SimpleXMLElement($contents);
            if (isset($additional_info->error)) return FALSE;
        } catch (Exception $e) {
            return FALSE;
        }
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

    private function updateFriendsList($community_id, $friends_list)
    {
        // Removing old records
        $sql = 'DELETE FROM friends WHERE user_community_id1 = :user_id;';
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
                ":friend_since" => $friend->friend_since));
            $statement->closeCursor();
        }
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

class User
{

    private $community_id;

    private $nickname;

    private $avatar_url;
    private $tag;
    private $creation_time;
    private $real_name;

    // Current status
    private $status;
    private $last_login_time;
    private $current_game_id;
    private $current_game_name;
    private $current_game_server_ip;

    // Bans info
    private $is_vac_banned;
    private $is_limited_account;
    private $trade_ban_state;

    // Location
    private $location_city_id;
    private $location_country_code;
    private $location_state_code;

    private $primary_group_id;
    private $last_updated;


    /**
     * @param $profile Object, containing information about user received from Steam API
     */
    function __construct($profile) {
        $this->community_id = $profile->community_id;
        $this->nickname = $profile->nickname;
        $this->avatar_url = $profile->avatar_url;
        $this->tag = $profile->tag;
        $this->creation_time = $profile->creation_time;
        $this->real_name = $profile->real_name;
        $this->status = $profile->status;
        $this->last_login_time = $profile->last_login_time;
        $this->current_game_id = $profile->current_game_id;
        $this->current_game_name = $profile->current_game_name;
        $this->current_game_server_ip = $profile->current_game_server_ip;
        $this->is_vac_banned = $profile->is_vac_banned;
        $this->is_limited_account = $profile->is_limited_account;
        $this->trade_ban_state = $profile->trade_ban_state;
        $this->location_city_id = $profile->location_city_id;
        $this->location_country_code = $profile->location_country_code;
        $this->location_state_code = $profile->location_state_code;
        $this->primary_group_id = $profile->primary_group_id;
        $this->last_updated = $profile->last_updated;

        $this->steam = new Locomotive();
    }

    public function getAvatarUrl() { return $this->avatar_url; }

    public function getCommunityId() { return $this->community_id; }

    /**
     * @return string Returns user's Steam ID
     */
    public function getSteamId() {
        return $this->steam->tools->users->convertToSteamID($this->community_id);
    }

    public function getStatus()
    {
        switch ($this->status) {
            case '1': return 'Online';
            case '2': return 'Busy';
            case '3': return 'Away';
            case '4': return 'Snooze';
            case '5': return 'Looking to trade';
            case '5': return 'Looking to play';
            case '0':
            default:
                return 'Offline';
        }
    }

    public function getCurrentGameId() { return $this->current_game_id; }

    public function isInGame() {
        if (isset($this->current_game_id)) return TRUE;
        else return FALSE;
    }

    public function getCurrentAppStorePageURL() {
        if (isset($this->current_game_id)) {
            return 'http://store.steampowered.com/app/' . $this->current_game_id;
        }
        return NULL;
    }

    public function getCurrentAppName() { return $this->current_game_name; }

    /**
     * @return null|string Returns connection URL if current server IP is set, NULL otherwise.
     */
    public function getConnectionUrl() {
        if (isset($this->current_game_server_ip)) {
            return 'steam://connect/' . $this->current_game_server_ip;
        }
        return NULL;
    }

    public function getCurrentGameServerIp() { return $this->current_game_server_ip; }

    public function isLimitedAccount() { return $this->is_limited_account; }
    public function isVacBanned() { return $this->is_vac_banned; }
    public function getTradeBanState() { return $this->trade_ban_state; }

    public function getLastLoginTime($raw = FALSE) {
        if ($raw) return $this->last_login_time;
        else return date(DATE_RFC850, $this->last_login_time);
    }

    public function getLastUpdateTime($raw = FALSE) {
        if ($raw) return $this->last_updated;
        else return date(DATE_RFC850, $this->last_updated);
    }

    /**
     * @return null|string Returns string containing location info (country, state, and city). NULL if there is no info.
     */
    public function getLocation()
    {
        $result = NULL;
        if (isset($this->location_country_code))
        {
            $result = $this->location_country_code;
            if (isset($this->location_state_code))
                $result .= ', ' . $this->location_state_code;
            if (isset($this->location_city_id))
                $result .= ', ' . $this->location_city_id;
        }
        return $result;
    }

    public function getLocationCountryCode() {
        if (isset($this->location_country_code))
            return strtoupper($this->location_country_code);
        return $this->location_country_code;
    }

    /**
     * @return string Returns string containing user's nickname
     */
    public function getNickname() { return (string)$this->nickname; }

    /**
     * @return mixed Returns ID of user's primary group
     */
    public function getPrimaryGroupId() { return $this->primary_group_id; }

    public function getRealName() { return $this->real_name; }

    public function getTag() { return $this->tag; }

    public function getBadgesHTML() {
        return $this->steam->tools->users->getBadges($this->community_id);
    }

    public function getCreationTime() {
        return date(DATE_RFC850, $this->creation_time);
    }

}