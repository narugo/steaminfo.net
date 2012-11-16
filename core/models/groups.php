<?php

class Groups_Model extends Model {

    function __construct() {
        parent::__construct();
        $this->steam = new Locomotive();
    }

    // TODO: Add ability to get group info by name
    public function getGroupInfo($group_id) {
        return $this->steam->communityapi->getGroupInfoById($group_id);
    }

    public function updateGroupInfo($group) {}

    public function getMembers($group_id) {}

}