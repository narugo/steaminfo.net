<?php

class Groups_Model extends Model {

    function __construct() {
        parent::__construct();
        $this->steam = new Locomotive();
    }

    public function search($query) {
        $query_type = $this->steam->tools->groups->getTypeOfQuery($query);
        switch ($query_type) {
            case TYPE_COMMUNITY_ID:
                try {
                    $info = $this->steam->communityapi->getGroupInfoById($query);
                    self::updateGroupInfo($info);
                    $result = self::getGroupInfo($info->groupID64);
                } catch (SteamAPIUnavailableException $e) {
                    $result = array(); // TODO: Return error message
                }
                break;
            case TYPE_VANITY:
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

    public function getGroupInfo($group_id) {
        $statement = $this->db->prepare('
                SELECT id,
                 avatar_icon_url,
                 avatar_medium_url,
                 avatar_full_url,
                 headline,
                 name,
                 summary,
                 url
                FROM groups
                WHERE id = :id
             ');
        $statement->execute(array(':id' => $group_id));
        // TODO: Check if found
        return $statement->fetchObject();
    }

    public function updateGroupInfo($group) {
        $sql = "INSERT INTO groups (
                 id,
                 avatar_icon_url,
                 avatar_medium_url,
                 avatar_full_url,
                 headline,
                 name,
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
                name = :name,
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

    public function getMembers($group_id) {}

    public function updateValveEmployeeTags() {
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