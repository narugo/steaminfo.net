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
        switch ($query_type) {
            case ID_TYPE_COMMUNITY:
                try {
                    $info = $this->steam->communityapi->getGroupInfoById($query);
                    self::updateGroupInfo($info);
                    $result = self::getGroupInfo($info->groupID64);
                } catch (SteamAPIUnavailableException $e) {
                    $result = array(); // TODO: Return error message
                }
                break;
            case ID_TYPE_VANITY:
                // TODO: Search in DB for other groups (maybe name has been requested, not Vanity URL)
                try {
                    $info = $this->steam->communityapi->getGroupInfoByName($query);
                    self::updateGroupInfo($info);
                    $result = self::getGroupInfo($info->groupID64);
                } catch (SteamAPIUnavailableException $e) {
                    $result = array(); // TODO: Return error message
                }
                break;
            default:
                // TODO: Search in DB for users with that (query) nickname
                $result = array();
        }
        return $result;
    }

    public function getUserGroups($community_id)
    {
        $cache_key = 'user_group_' . $community_id;
        $groups = $this->memcached->get($cache_key);
        if ($groups == FALSE) {
            $response = $this->steam->ISteamUser->GetUserGroupList($community_id);
            self::addGroupMember($response->response->groups, $community_id);

            $statement = $this->db->prepare('SELECT id, `name`, url, avatar_url FROM group_members
                INNER JOIN `group` ON group_members.group_id = `group`.id
                WHERE user_community_id = :id');
            $statement->execute(array(':id' => $community_id));
            $groups = $statement->fetchAll(PDO::FETCH_OBJ);
            $this->memcached->add($cache_key, $groups, 60);
        }
        return $groups;
    }

    public function addGroupMember($groups, $community_id)
    {
        $sql = 'INSERT INTO `group` (id) VALUES (:gid);
            INSERT INTO group_members (user_community_id, group_id) VALUES (:user_id, :gid)';
        $statement = $this->db->prepare($sql);
        foreach ($groups as $group) {
            $statement->execute(array(':user_id' => $community_id,
                ':gid' => $group->gid));
            $statement->closeCursor();
        }
    }

    public function getGroup($name)
    {
        $cache_key = 'group_' . $name;
        $group = $this->memcached->get($cache_key);
        if ($group == FALSE) {
            $id = self::updateGroupInfo($name);

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

    public function updateGroupInfo($group_name)
    {
        $group = $this->steam->communityapi->getGroupInfoByName($group_name);
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

    public function getGroupMembers($group_id)
    {
        // TODO: Implement
    }

    public function updateValveEmployeeTags()
    {
        try {
            $valve_group_info = $this->steam->communityapi->getGroupInfoByName("Valve");
            // TODO: Find a better way to access other models
            require_once 'users.php';
            $users_model = new Users_Model();
            foreach ($valve_group_info->members->steamID64 as $valve_employee_id) {
                $users_model->setTag((string)$valve_employee_id, 'Valve Employee');
                echo 'Updated user #' . $valve_employee_id . '<br />';
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