<?php

class Groups_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function search($query)
    {
        $query_type = $this->steam->tools->groups->getTypeOfId($query);
        try {
            switch ($query_type) {
                case GROUP_ID_TYPE_STEAM:
                    $response = $this->steam->communityapi->getGroupInfoById($query);
                    self::updateGroupInfo($response);
                    $result = self::getGroup($response->groupID64, TRUE);
                    break;
                case GROUP_ID_TYPE_VANITY:
                    // TODO: Search in DB for other groups (maybe name has been requested, not Vanity URL)
                    $response = $this->steam->communityapi->getGroupInfoByName($query);
                    self::updateGroupInfo($response);
                    $result = self::getGroup($response->groupID64, TRUE);
                    break;
                default:
                    // TODO: Search in DB for users with that (query) nickname
                    $result = array();
            }
        } catch (SteamAPIUnavailableException $e) {
            $result = array(); // TODO: Return error message
        }
        return $result;
    }

    public function getSearchSuggestions($input)
    {
        $cache_key = 'groups_suggestions_for_' . $input;
        $suggestions = $this->memcached->get($cache_key);
        if ($suggestions === FALSE) {
            // TODO: Find a way to get better suggestions
            $statement = $this->db->prepare('
                SELECT *
                FROM `group`
                WHERE SOUNDEX(`name`) = SOUNDEX(:input)
                LIMIT 0, 5
            ');
            $statement->execute(array(":input" => $this->db->quote($input)));
            $suggestions = $statement->fetchAll();
            $this->memcached->add($cache_key, $suggestions, 3600);
        }
        return $suggestions;
    }

    public function getTop10()
    {
        $cache_key = 'top_10_groups';
        $top = $this->memcached->get($cache_key);
        if ($top === FALSE) {
            $yesterday = time() - 86400;
            $sql = 'SELECT id, name, avatar_icon_url, unique_requests
                    FROM (
                        SELECT group_id, count(group_id) as unique_requests
                        FROM group_view_log
                        WHERE `time` > FROM_UNIXTIME(' . $yesterday . ')
                        GROUP BY group_id
                        ORDER BY unique_requests DESC
                        LIMIT 10
                    ) top
                    INNER JOIN `group` ON `group`.id = group_id';
            $statement = $this->db->query($sql);
            $top = $statement->fetchAll(PDO::FETCH_OBJ);
            $this->memcached->add($cache_key, $top, 1800);
        }
        return $top;
    }

    public function getUserGroups($community_id)
    {
        $cache_key = 'user_groups_' . $community_id;
        $groups = $this->memcached->get($cache_key);
        if ($groups === FALSE) {
            $response = $this->steam->ISteamUser->GetUserGroupList($community_id);
            self::addGroupMember($response->response->groups, $community_id);

            $statement = $this->db->prepare('SELECT `group`.*
                FROM group_members
                INNER JOIN `group` ON group_members.group_id = `group`.id
                WHERE user_community_id = :id');
            $statement->execute(array(':id' => $community_id));
            $groups = $statement->fetchAll(PDO::FETCH_OBJ);
            $this->memcached->add($cache_key, $groups, 1600);
        }
        return $groups;
    }

    public function addGroupMember($groups, $community_id)
    {
        // Removing old records
        $sql = 'DELETE FROM group_members WHERE user_community_id= :user_id;';
        $statement = $this->db->prepare($sql);
        $statement->execute(array(":user_id" => $community_id));
        $statement->closeCursor();

        $sql = 'INSERT IGNORE INTO `group` (id) VALUES (:gid);
            INSERT INTO group_members (user_community_id, group_id) VALUES (:user_id, :gid)';
        $statement = $this->db->prepare($sql);
        foreach ($groups as $group) {
            $statement->execute(array(
                ':user_id' => $community_id,
                ':gid' => ($group->gid + 103582791429521408)
            ));
            $statement->closeCursor();
        }
    }

    public function getGroup($id, $no_update = FALSE)
    {
        $cache_key = 'group_' . $id;
        $group = $this->memcached->get($cache_key);
        if ($group === FALSE) {
            if (!$no_update) {
                $group = $this->steam->communityapi->getGroupInfoById($id);
                self::updateGroupInfo($group);
            }

            $statement = $this->db->prepare('
                SELECT id,
                 avatar_icon_url,
                 avatar_medium_url,
                 avatar_full_url,
                 headline,
                 `name`,
                 summary,
                 url
                FROM `group`
                WHERE id = :id
             ');
            $statement->execute(array(':id' => $id));
            $group = new Group($statement->fetchObject());
            $this->memcached->add($cache_key, $group);
        }
        return $group;
    }

    public function updateGroupInfo($group)
    {
        $sql = "INSERT INTO `group` (
                 id,
                 avatar_icon_url,
                 avatar_medium_url,
                 avatar_full_url,
                 headline,
                 `name`,
                 summary,
                 url)
            VALUES (
                :id,
                :avatar_icon_url,
                :avatar_medium_url,
                :avatar_full_url,
                :headline,
                :name,
                :summary,
                :url)
            ON DUPLICATE KEY UPDATE
                id = :id,
                avatar_icon_url = :avatar_icon_url,
                avatar_medium_url = :avatar_medium_url,
                avatar_full_url = :avatar_full_url,
                headline = :headline,
                `name` = :name,
                summary = :summary,
                url = :url";
        $statement = $this->db->prepare($sql);
        $statement->execute(array(
            ":id" => $group->groupID64,
            ":avatar_icon_url" => $group->groupDetails->avatarIcon,
            ":avatar_medium_url" => $group->groupDetails->avatarMedium,
            ":avatar_full_url" => $group->groupDetails->avatarFull,
            ":headline" => $group->groupDetails->headline,
            ":name" => $group->groupDetails->groupName,
            ":summary" => $group->groupDetails->summary,
            ":url" => $group->groupDetails->groupURL));
        return $group->groupID64;

        // TODO: Update group members in database
        /*
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
                }*/
    }

    public
    function getGroupMembers($group_id)
    {
        // TODO: Implement
    }

    public
    function updateValveEmployeeTags()
    {
        try {
            $valve_group_info = $this->steam->communityapi->getGroupInfoByName("Valve");
            require_once PATH_TO_MODELS . 'users.php';
            $users_model = new Users_Model();
            $i = 1;
            foreach ($valve_group_info->members->steamID64 as $valve_employee_id) {
                $users_model->setTag((string)$valve_employee_id, 'Valve Employee');
                echo $i++ . '. Updated user #' . $valve_employee_id . '<br />';
            }
        } catch (Exception $e) {
            echo 'Error!';
        }
    }

}

class Group
{

    public $id;
    public $name;
    public $avatar_icon_url;
    public $avatar_medium_url;
    public $avatar_full_url;
    public $headline;
    public $summary;
    public $url;
    public $last_updated;

    function __construct($group)
    {
        $this->id = $group->id;
        $this->name = $group->name;
        $this->avatar_icon_url = $group->avatar_icon_url;
        $this->avatar_medium_url = $group->avatar_medium_url;
        $this->avatar_full_url = $group->avatar_full_url;
        $this->headline = $group->headline;
        $this->summary = $group->summary;
        $this->url = $group->url;
        $this->last_updated = $group->last_updated;
    }

    public function getAvatarFullUrl()
    {
        return $this->avatar_full_url;
    }

    public function getAvatarIconUrl()
    {
        return $this->avatar_icon_url;
    }

    public function getAvatarMediumUrl()
    {
        return $this->avatar_medium_url;
    }

    public function getHeadline()
    {
        return $this->headline;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLastUpdated()
    {
        return $this->last_updated;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function getUrl()
    {
        return $this->url;
    }

}