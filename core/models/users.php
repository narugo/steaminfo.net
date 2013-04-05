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
            case USER_ID_TYPE_COMMUNITY:
                $result = self::getProfileSummary($query);
                break;
            case USER_ID_TYPE_STEAM:
                $community_id = $this->steam->tools->users->steamIdToCommunityId($query);
                $result = self::getProfileSummary($community_id);
                break;
            case USER_ID_TYPE_VANITY:
                $response = $this->steam->ISteamUser->ResolveVanityURL($query);
                $result = self::getProfileSummary($response->response->steamid);
                break;
            default:
                // TODO: Search in DB for users with that (query) nickname
                $result = array();
        }
        return $result;
    }

    public function getProfileSummary($community_id)
    {
        $cache_key = 'profile_summary_of_' . $community_id;
        $profile = $this->memcached->get($cache_key);
        if ($profile === FALSE) {
            self::updateSummaries(array($community_id));
            // Updating bans info
            self::updateBanStatuses(array($community_id));
            $sql = 'SELECT steam_user.*, app.name AS current_app_name
                    FROM steam_user
                    LEFT OUTER JOIN app ON steam_user.current_game_id = app.id
                    WHERE community_id = :id';
            $statement = $this->db->prepare($sql);
            $statement->execute(array(':id' => $community_id));
            $profile = $statement->fetchObject();
            $this->memcached->add($cache_key, $profile, 3600);
        }
        return $profile;
    }

    public function updateSummaries($community_ids)
    {
        $summaries = array();
        foreach (array_chunk($community_ids, 100) as $chunk) {
            $result = $this->steam->ISteamUser->GetPlayerSummaries($chunk);
            $summaries = array_merge($summaries, $result->response->players);
        }
        foreach ($summaries as $summary) {
            $this->db->upsert(
                'steam_user',
                array('community_id' => $summary->steamid),
                array(
                    "nickname" => $summary->personaname,
                    "avatar_url" => $summary->avatar,
                    "creation_time" => $summary->timecreated,
                    "real_name" => $summary->realname,
                    "location_country_code" => $summary->loccountrycode,
                    "location_state_code" => $summary->locstatecode,
                    "location_city_id" => $summary->loccityid,
                    "last_login_time" => $summary->lastlogoff,
                    "status" => $summary->personastate,
                    "current_game_server_ip" => $summary->gameserverip,
                    "current_game_name" => $summary->gameextrainfo,
                    "current_game_id" => $summary->gameid,
                    "primary_group_id" => $summary->primaryclanid
                )
            );
        }
    }

    private function updateBanStatuses($ids)
    {
        $response = $this->steam->ISteamUser->GetPlayerBans($ids);
        foreach ($response->players as $bans) {
            $this->db->upsert(
                'steam_user',
                array("community_id" => $bans->SteamId),
                array(
                    "is_community_banned" => $bans->CommunityBanned ? 'TRUE' : 'FALSE',
                    "is_vac_banned" => $bans->VACBanned ? 'TRUE' : 'FALSE',
                    "economy_ban_state" => $bans->EconomyBan
                )
            );
        }
    }

    public function getSearchSuggestions($input)
    {
        $cache_key = 'users_suggestions_for_' . $input;
        $suggestions = $this->memcached->get($cache_key);
        if ($suggestions === FALSE) {
            // TODO: Find a way to get better suggestions
            $statement = $this->db->prepare('
                SELECT community_id, nickname, avatar_url, tag
                FROM steam_user
                WHERE nickname LIKE :input
            ');
            $statement->execute(array(":input" => $this->db->quote($input)));
            $suggestions = $statement->fetchAll();
            $this->memcached->add($cache_key, $suggestions, 3600);
        }
        return $suggestions;
    }

    public function getFriends($community_id)
    {
        $cache_key = 'friends_of_' . $community_id;
        $friends = $this->memcached->get($cache_key);
        if ($friends === FALSE) {
            self::updateFriendsList($community_id);
            $statement = $this->db->prepare('SELECT community_id, nickname, avatar_url, tag, since FROM friends
                INNER JOIN steam_user ON friends.user_community_id2 = steam_user.community_id
                WHERE user_community_id1 = :id');
            $statement->execute(array(':id' => $community_id));
            $friends = $statement->fetchAll(PDO::FETCH_OBJ);
            $this->memcached->add($cache_key, $friends, 3600);
        }
        return $friends;
    }

    private function updateFriendsList($community_id)
    {
        $response = $this->steam->ISteamUser->GetFriendList($community_id);
        $friends_list = $response->friendslist->friends;

        if (empty($friends_list)) return;

        $this->db->beginTransaction();

        // Removing old friends
        $remove_old = 'DELETE FROM friends WHERE user_community_id1 = :user_id;';
        $statement = $this->db->prepare($remove_old);
        $statement->execute(array(":user_id" => $community_id));
        $statement->closeCursor();

        // Adding unknown users
        foreach ($friends_list as $friend) {
            $sql = 'INSERT INTO steam_user (community_id) SELECT ' . $friend->steamid
                . 'WHERE NOT EXISTS (SELECT 1 FROM steam_user WHERE community_id=' . $friend->steamid . ')';
            $this->db->query($sql);
        }

        // Adding new friends
        $insert_profiles = 'INSERT INTO friends (user_community_id1, user_community_id2, since) VALUES ';
        foreach ($friends_list as $friend) {
            if (empty($friend->friend_since)) $friend->friend_since = "NULL";
            $insert_profiles = $insert_profiles . "($community_id, $friend->steamid, $friend->friend_since),";
        }
        $insert_profiles = substr($insert_profiles, 0, -1) . ";";
        $this->db->query($insert_profiles);

        $this->db->commit();
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
                        WHERE time > \'' . date('Y-m-d H:i:s', $yesterday) . '\'
                        GROUP BY user_id
                        ORDER BY unique_requests DESC
                        LIMIT 10
            ) top
            INNER JOIN steam_user ON steam_user.community_id = user_id';
            $statement = $this->db->query($sql);
            $top = $statement->fetchAll(PDO::FETCH_OBJ);
            $this->memcached->add($cache_key, $top, 1800);
        }
        return $top;
    }

    public function setTag($community_id, $tag)
    {
        $this->db->upsert(
            'steam_user',
            array('community_id' => $community_id),
            array('tag' => $tag)
        );
    }

}