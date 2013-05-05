<?php
use SteamInfo\Models\Entities\Application;
use SteamInfo\Models\Entities\AppOwner;
use SteamInfo\Models\Entities\Friends;
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
            $result = self::getUsersFromSteam(array($id));
            $user = $result[0];
            $this->memcached->add($cache_key, $user, 3600);
        }
        return $user;
    }

    /**
     * @param $ids
     * @param bool $updateBans
     * @return User[]
     */
    private function getUsersFromSteam($ids, $updateBans = true)
    {
        self::updateSummaries($ids);
        if ($updateBans) self::updateBans($ids);

        $result = array();
        $userRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\User');
        foreach ($ids as $id) {
            $user = $userRepository->find($id);
            if (!empty($user)) array_push($result, $user);
        }
        return $result;
    }

    private function updateSummaries($ids)
    {
        $userRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\User');

        $profiles = $this->steam->ISteamUser->GetPlayerSummaries($ids);
        foreach ($profiles->response->players as $profile) {
            $user = $userRepository->find($profile->steamid);
            if (empty($user)) $user = new User();

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
            if (isset($profile->gameid))
                if (!($profile->gameid > 9223372036854775807)) // Max BIGINT
                    $user->setCurrentAppId($profile->gameid);
            if (isset($profile->primaryclanid)) $user->setPrimaryGroupId($profile->primaryclanid);

            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }

    private function updateBans($ids)
    {
        $userRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\User');

        $bans = $this->steam->ISteamUser->GetPlayerBans($ids);
        foreach ($bans->players as $bans_user) {
            $user = $userRepository->find($bans_user->SteamId);
            if (empty($user)) continue;

            $user->setCommunityBanState($bans_user->CommunityBanned);
            $user->setVacBanState($bans_user->VACBanned);
            $user->setEconomyBanState($bans_user->EconomyBan);

            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }

    public function getFriends($user_id)
    {
        $cache_key = $user_id . '_friends';
        $friends = $this->memcached->get($cache_key);
        if ($friends === FALSE) {
            self::removeOldFriends($user_id);

            $response = $this->steam->ISteamUser->GetFriendList($user_id);
            $friend_ids = array();
            foreach ($response->friendslist->friends as $friend) {
                array_push($friend_ids, $friend->steamid);
            }

            $userRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\User');
            $user = $userRepository->find($user_id);

            $friend_profiles = self::getUsersFromSteam($friend_ids, false);
            $friends = array();

            $friendsRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\Friends');
            foreach ($friend_profiles as $friend) {
                $current_friend = $friendsRepository->findOneBy(array(
                    'user' => $user->getId(),
                    'friend' => $friend->getId()
                ));
                if (empty($current_friend)) {
                    $current_friend = new Friends();
                    $current_friend->setUser($user);
                    $current_friend->setFriend($friend);
                    $key = array_search($friend->getId(), $friend_ids);
                    $friends_since_timestamp = $response->friendslist->friends[$key]->friend_since;
                    if (!empty($friends_since_timestamp)) {
                        $friends_since = date_create();
                        date_timestamp_set($friends_since, $friends_since_timestamp);
                        $current_friend->setSince($friends_since);
                    }
                    $this->entityManager->persist($current_friend);
                }

                array_push($friends, $current_friend);
            }
            $this->entityManager->flush();

            // TODO: Fix (wrong data is returned from cache)
            //$this->memcached->add($cache_key, $friends, 3600);
        }
        return $friends;
    }

    private function removeOldFriends($user_id)
    {
        $friendsRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\Friends');
        $old_friends = $friendsRepository->findBy(array('user' => $user_id));
        foreach ($old_friends as $old_friend) {
            $this->entityManager->remove($old_friend);
        }
        $this->entityManager->flush();
    }

    public function getOwnedApps($user_id)
    {
        $cache_key = $user_id . '_apps';
        $apps = $this->memcached->get($cache_key);
        if ($apps === FALSE) {
            self::removeOwnedApps($user_id);

            $response = $this->steam->IPlayerService->GetOwnedGames($user_id, true, true);

            $userRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\User');
            $user = $userRepository->find($user_id);

            $apps = array();

            $applicationsRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\Application');
            $appOwnerRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\AppOwner');
            foreach ($response->response->games as $app) {
                $application = $applicationsRepository->find($app->appid);
                if (empty($application)) {
                    $application = new Application();
                    $application->setId($app->appid);
                    $application->setName($app->name);
                    if (!empty($app->img_icon_url)) $application->setIcon($app->img_icon_url);
                    if (!empty($app->img_logo_url)) $application->setLogo($app->img_logo_url);
                    if (!empty($app->has_community_visible_stats)) $application->setHasCommunityVisibleStats($app->has_community_visible_stats);

                    $this->entityManager->persist($application);
                    $this->entityManager->flush();
                }

                $current_app_owner = $appOwnerRepository->findOneBy(array(
                    'application' => $app->appid,
                    'user' => $user->getId()
                ));
                if (empty($current_app_owner)) $current_app_owner = new AppOwner();
                $current_app_owner->setUser($user);
                $current_app_owner->setApplication($application);
                if (isset($app->playtime_forever)) $current_app_owner->setUsedTotal($app->playtime_forever);
                if (isset($app->playtime_2weeks)) $current_app_owner->setUsedInLast2Weeks($app->playtime_2weeks);

                $this->entityManager->persist($current_app_owner);

                array_push($apps, $current_app_owner);
            }
            $this->entityManager->flush();

            $this->memcached->add($cache_key, $apps, 3600);
        }
        return $apps;
    }

    private function removeOwnedApps($user_id)
    {
        $appOwnerRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\AppOwner');
        $old_apps = $appOwnerRepository->findBy(array('user' => $user_id));
        foreach ($old_apps as $old_app) {
            $this->entityManager->remove($old_app);
        }
        $this->entityManager->flush();
    }

    public function updateSummariesOLD($community_ids)
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