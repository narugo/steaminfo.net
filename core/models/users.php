<?php
use SteamInfo\Models\Entities\User;

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
        $query_type = $this->steam->tools->user->getTypeOfId($query);
        switch ($query_type) {
            case USER_ID_TYPE_COMMUNITY:
                $result = self::getUser($query);
                break;
            case USER_ID_TYPE_STEAM:
                $community_id = $this->steam->tools->user->steamIdToCommunityId($query);
                var_dump($community_id);
                $result = self::getUser($community_id);
                break;
            case USER_ID_TYPE_VANITY:
                $response = $this->steam->ISteamUser->ResolveVanityURL($query);
                $community_id = $response->response->steamid;
                $result = self::getUser($community_id);
                break;
            default:
                // TODO: Search in DB
                $result = array();
        }
        return $result;
    }

    public function getUser($id)
    {
        $cache_key = 'user_' . $id;
        $user = $this->memcached->get($cache_key);
        if ($user === FALSE) {
            $userRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\User');
            $user = $userRepository->find($id);
            if (empty($user)) $user = new User();

            // Updating profile
            var_dump($id);
            $response_profile = $this->steam->ISteamUser->GetPlayerSummaries(array($id));
            // TODO: Improve error handling
            if (empty($response_profile->response->players[0])) return false;
            $profile = $response_profile->response->players[0];
            $user->setId($profile->steamid);
            $user->setNickname($profile->personaname);
            $user->setAvatarUrl($profile->avatar);
            if (isset($profile->timecreated)) {
                $creation_time = date_create();
                date_timestamp_set($creation_time, $profile->timecreated);
                $user->setCreationTime($creation_time);
            }
            if (isset($profile->realname)) $user->setRealName($profile->realname);
            if (isset($profile->loccountrycode)) $user->setLocationCountryCode($profile->loccountrycode);
            if (isset($profile->locstatecode)) $user->setLocationStateCode($profile->locstatecode);
            if (isset($profile->loccityid)) $user->setLocationCityId($profile->loccityid);
            if (isset($profile->lastlogoff)) {
                $last_login = date_create();
                date_timestamp_set($last_login, $profile->lastlogoff);
                $user->setLastLoginTime($last_login);
                $user->setStatus($profile->personastate);
            }
            if (isset($profile->gameserverip)) $user->setCurrentServerIp($profile->gameserverip);
            if (isset($profile->gameextrainfo)) $user->setCurrentAppName($profile->gameextrainfo);
            if (isset($profile->gameid)) $user->setCurrentAppId($profile->gameid);
            if (isset($profile->primaryclanid)) $user->setPrimaryGroupId($profile->primaryclanid);

            // Updating bans
            $response_bans = $this->steam->ISteamUser->GetPlayerBans(array($id));
            $bans = $response_bans->players[0];
            $user->setCommunityBanState($bans->CommunityBanned);
            $user->setVacBanState($bans->VACBanned);
            $user->setEconomyBanState($bans->EconomyBan);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Saving in cache
            $this->memcached->add($cache_key, $user, 3600);
        }
        return $user;
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