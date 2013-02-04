<?php

/**
 * Database interface
 */
class Users_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function search($query)
    {
        $query_type = $this->steam->tools->users->getTypeOfId($query);
        switch ($query_type) {
            case ID_TYPE_COMMUNITY:
                $result = self::getProfileSummary($query);
                break;
            case ID_TYPE_STEAM:
                $community_id = $this->steam->tools->users->steamIdToCommunityId($query);
                $result = self::getProfileSummary($community_id);
                break;
            case ID_TYPE_VANITY:
                $response = $this->steam->ISteamUser->ResolveVanityURL($query);
                $result = self::getProfileSummary($response->response->steamid);
                break;
            default:
                // TODO: Search in DB for users with that (query) nickname
                $result = array();
        }
        return $result;
    }

    public function getSearchSuggestions($input)
    {
        $cache_key = 'users_suggestions_for_' . $input;
        $suggestions = $this->memcached->get($cache_key);
        if ($suggestions === FALSE) {
            // TODO: Find a way to get better suggestions
            $statement = $this->db->prepare('
                SELECT community_id, nickname, avatar_url, tag
                FROM user
                WHERE SOUNDEX(nickname) = SOUNDEX(:input)
                LIMIT 0, 5
            ');
            $statement->execute(array(":input" => $this->db->quote($input)));
            $suggestions = $statement->fetchAll();
            $this->memcached->add($cache_key, $suggestions, 3600);
        }
        return $suggestions;
    }

    public function getProfileSummary($community_id)
    {
        $cache_key = 'profile_summary_of_' . $community_id;
        $profile = $this->memcached->get($cache_key);
        if ($profile === FALSE) {
            self::updateSummaries(array($community_id));
            // Updating bans info
            self::updateBanStatuses(array($community_id));
            $statement = $this->db->prepare('SELECT * FROM `user` WHERE community_id=:id');
            $statement->execute(array(':id' => $community_id));
            $profile = new User($statement->fetchObject());
            $this->memcached->add($cache_key, $profile, 3600);
        }
        return $profile;
    }

    public function getFriends($community_id)
    {
        $cache_key = 'friends_of_' . $community_id;
        $friends = $this->memcached->get($cache_key);
        if ($friends === FALSE) {
            self::updateFriendsList($community_id);

            $statement = $this->db->prepare('SELECT community_id, nickname, avatar_url, tag, since FROM friends
                INNER JOIN `user` ON friends.user_community_id2 = `user`.community_id
                WHERE user_community_id1 = :id');
            $statement->execute(array(':id' => $community_id));
            $friends = $statement->fetchAll(PDO::FETCH_OBJ);
            $this->memcached->add($cache_key, $friends, 3600);
        }
        return $friends;
    }

    public function getTop10()
    {
        $cache_key = 'top_10_users';
        $top = $this->memcached->get($cache_key);
        if ($top === FALSE) {
            $yesterday = time() - 86400;
            $sql = 'SELECT community_id, nickname, tag, avatar_url, unique_requests
                    FROM (
                        SELECT user_id, count(user_id) as unique_requests
                        FROM user_profile_view_log
                        WHERE `time` > FROM_UNIXTIME(' . $yesterday . ')
                        GROUP BY user_id
                        ORDER BY unique_requests DESC
                        LIMIT 10
                    ) top
                    INNER JOIN `user` ON `user`.community_id = user_id';
            $statement = $this->db->query($sql);
            $top = $statement->fetchAll(PDO::FETCH_OBJ);
            $this->memcached->add($cache_key, $top, 1800);
        }
        return $top;

    }

    public function setTag($community_id, $tag)
    {
        // TODO: Validate ID
        // TODO: Modify function so it can get multiple IDs
        $sql = "INSERT INTO `user` (community_id, tag) VALUES (:id, :tag)
                ON DUPLICATE KEY UPDATE community_id = :id, tag = :tag";
        $statement = $this->db->prepare($sql);
        return $statement->execute(array(':id' => $community_id, ':tag' => $tag));
    }

    public function updateSummaries($community_ids)
    {
        // Updating profile summary
        $summaries = array();
        foreach (array_chunk($community_ids, 100) as $chunk) {
            $result = $this->steam->ISteamUser->GetPlayerSummaries($chunk);
            $summaries = array_merge($summaries, $result->response->players);
        }

        // TODO: Add primary group to "groups" table
        $sql = "INSERT INTO `user` (
                community_id,
                nickname,
                avatar_url,
                creation_time,
                real_name,
                location_country_code,
                location_state_code,
                location_city_id,
                last_login_time,
                `status`,
                current_game_server_ip,
                current_game_name,
                current_game_id,
                primary_group_id,
                last_updated)
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
                :primary_group_id,
                CURRENT_TIMESTAMP)
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
                `status` = :status,
                current_game_server_ip = :current_game_server_ip,
                current_game_name = :current_game_name,
                current_game_id = :current_game_id,
                primary_group_id = :primary_group_id,
                last_updated = CURRENT_TIMESTAMP";
        $statement = $this->db->prepare($sql);
        foreach ($summaries as $summary) {
            $statement->execute(array(
                ":community_id" => $summary->steamid,
                ":nickname" => $summary->personaname,
                ":avatar_url" => $summary->avatar,
                ":creation_time" => $summary->timecreated,
                ":real_name" => $summary->realname,
                ":location_country_code" => $summary->loccountrycode,
                ":location_state_code" => $summary->locstatecode,
                ":location_city_id" => $summary->loccityid,
                ":last_login_time" => $summary->lastlogoff,
                ":status" => $summary->personastate,
                ":current_game_server_ip" => $summary->gameserverip,
                ":current_game_name" => $summary->gameextrainfo,
                ":current_game_id" => $summary->gameid,
                ":primary_group_id" => $summary->primaryclanid
            ));
            $statement->closeCursor();
        }
    }

    private function updateBanStatuses($ids)
    {
        $response = $this->steam->ISteamUser->GetPlayerBans($ids);
        $sql = 'UPDATE `user`
                SET is_community_banned = :is_community_banned,
                    is_vac_banned = :is_vac_banned,
                    economy_ban_state = :economy_ban_state
                WHERE community_id = :id';
        $statement = $this->db->prepare($sql);
        foreach ($response->players as $bans) {
            $statement->execute(array(
                ":is_community_banned" => $bans->CommunityBanned,
                ":is_vac_banned" => $bans->VACBanned,
                ":economy_ban_state" => $bans->EconomyBan,
                ":id" => $bans->SteamId
            ));
        }
    }

    private function updateFriendsList($community_id)
    {
        $response = $this->steam->ISteamUser->GetFriendList($community_id);
        $friends_list = $response->friendslist->friends;

        // Removing old friends
        $remove_old = 'DELETE FROM friends WHERE user_community_id1 = :user_id;';
        $statement = $this->db->prepare($remove_old);
        $statement->execute(array(":user_id" => $community_id));
        $statement->closeCursor();

        // Adding unknown users
        $insert_users = 'INSERT IGNORE INTO `user` (community_id) VALUES ';
        foreach ($friends_list as $friend) {
            $insert_users = $insert_users . "($friend->steamid),";
        }
        $insert_users = substr($insert_users, 0, -1) . ";";
        $this->db->query($insert_users);

        // Adding new friends
        $insert_profiles = 'INSERT INTO friends (user_community_id1, user_community_id2, since) VALUES ';
        foreach ($friends_list as $friend) {
            if (empty($friend->friend_since)) $friend->friend_since = "NULL";
            $insert_profiles = $insert_profiles . "($community_id, $friend->steamid, $friend->friend_since),";
        }
        $insert_profiles = substr($insert_profiles, 0, -1) . ";";
        $this->db->query($insert_profiles);
    }

}

class User
{

    public $community_id;

    public $nickname;

    public $avatar_url;
    public $tag;
    public $creation_time;
    public $real_name;

    // Current status
    public $status;
    public $last_login_time;
    public $current_game_id;
    public $current_game_name;
    public $current_game_server_ip;

    // Bans info
    public $is_vac_banned;
    public $is_community_banned;
    public $economy_ban_state;

    // Location
    public $location_city_id;
    public $location_country_code;
    public $location_state_code;

    public $primary_group_id;
    public $last_updated;

    function __construct($profile)
    {
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
        $this->is_community_banned = $profile->is_community_banned;
        $this->economy_ban_state = $profile->economy_ban_state;
        $this->location_city_id = $profile->location_city_id;
        $this->location_country_code = $profile->location_country_code;
        $this->location_state_code = $profile->location_state_code;
        $this->primary_group_id = $profile->primary_group_id;
        $this->last_updated = $profile->last_updated;
    }

    public function getAvatarUrl()
    {
        return $this->avatar_url;
    }

    public function getCommunityId()
    {
        return $this->community_id;
    }

    public function getSteamId()
    {
        $steam = new Locomotive();
        return $steam->tools->users->communityIdToSteamId($this->community_id);
    }

    public function getStatus()
    {
        switch ($this->status) {
            case '1':
                return 'Online';
            case '2':
                return 'Busy';
            case '3':
                return 'Away';
            case '4':
                return 'Snooze';
            case '5':
                return 'Looking to trade';
            case '6':
                return 'Looking to play';
            case '0':
            default:
                return 'Offline';
        }
    }

    public function getCurrentGameId()
    {
        return $this->current_game_id;
    }

    public function isInGame()
    {
        if (isset($this->current_game_id)) return TRUE;
        else return FALSE;
    }

    public function getCurrentAppStorePageURL()
    {
        if (isset($this->current_game_id)) {
            return 'http://store.steampowered.com/app/' . $this->current_game_id;
        }
        return NULL;
    }

    public function getCurrentAppName()
    {
        return $this->current_game_name;
    }

    /**
     * @return null|string Returns connection URL if current server IP is set, NULL otherwise.
     */
    public function getConnectionUrl()
    {
        if (isset($this->current_game_server_ip)) {
            return 'steam://connect/' . $this->current_game_server_ip;
        }
        return NULL;
    }

    public function getCurrentGameServerIp()
    {
        return $this->current_game_server_ip;
    }

    public function isCommunityBanned()
    {
        return $this->is_community_banned;
    }

    public function isVacBanned()
    {
        return $this->is_vac_banned;
    }

    public function getEconomyBanState()
    {
        return $this->economy_ban_state;
    }

    public function getLastLoginTime($raw = FALSE)
    {
        if ($raw) return $this->last_login_time;
        else return date(DATE_RFC850, $this->last_login_time);
    }

    public function getLastUpdateTime()
    {
        return $this->last_updated;
    }

    /**
     * @return null|string
     */
    public function getLocation()
    {
        $result = NULL;
        if (isset($this->location_country_code)) {
            $result = $this->location_country_code;
            if (isset($this->location_state_code))
                $result .= ', ' . $this->location_state_code;
            if (isset($this->location_city_id))
                $result .= ', ' . $this->location_city_id;
        }
        return $result;
    }

    public function getLocationCountryCode()
    {
        if (isset($this->location_country_code))
            return strtoupper($this->location_country_code);
        return $this->location_country_code;
    }

    public function getNickname()
    {
        return (string)$this->nickname;
    }

    public function getPrimaryGroupId()
    {
        return $this->primary_group_id;
    }

    public function getRealName()
    {
        return $this->real_name;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function getBadgesHTML()
    {
        $steam = new Locomotive();
        return $steam->tools->users->getBadges($this->community_id);
    }

    public function getCreationTime()
    {
        if (isset($this->creation_time))
            return date(DATE_RFC850, $this->creation_time);
        return NULL;
    }

}