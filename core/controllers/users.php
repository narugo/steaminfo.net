<?php

class Users extends Controller {

    function __construct() {
        parent::__construct();
        $this->loadModel("users");
    }

    function index() {
        $this->view->render("users/index");
    }

    function profile($params) {
        $community_id = self::get_community_id($params[0]);
        $responce = $this->model->getProfile($community_id);
        $this->view->profile = $responce['profile'];
        $this->view->update_status = $responce['update_status'];
        $this->view->render("users/profile");
    }

    function apps($params) {
        // TODO: Check if ID conversion is necessary
        $community_id = self::get_community_id($params[0]);
        $responce = $this->model->getApps($community_id);
        $this->view->apps = $responce['apps'];
        $this->view->update_status = $responce['update_status'];
        $this->view->render("users/includes/apps", TRUE);
    }

    function friends($params) {
        // TODO: Check if ID conversion is necessary
        $community_id = self::get_community_id($params[0]);
        $responce = $this->model->getFriends($community_id);
        $this->view->friends = $responce['friends'];
        $this->view->update_status = $responce['update_status'];
        $this->view->render("users/includes/friends", TRUE);
    }

    function groups($params) {
        // TODO: Check if ID conversion is necessary
        $community_id = self::get_community_id($params[0]);
        $this->view->groups = $this->model->getGroups($community_id);
        $this->view->render("users/includes/groups", TRUE);
    }


    /**
     * Support functions
     */
    // TODO: Think about moving these functions to another file (create a lib?)

    private function get_community_id($input) {
        switch (self::get_type_of_input($input)) {
            case "communityid":
                return $input;
            case "vanity":
                return self::resolve_vanity_url($input);
            case "steamid":
                return self::convertToCommunityID($input);
            default:
                return FALSE;
        }
    }

    private function resolve_vanity_url($name)
    {
        $url = 'http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?key='.STEAM_API_KEY.'&vanityurl='.$name;
        $contents = @file_get_contents($url);
        if ($contents === FALSE) return FALSE;
        $json = json_decode($contents);
        if ($json->response->success == '1') return $json->response->steamid;
        return NULL; // Profile was not found
    }

    private function get_type_of_input($query) {
        if (self::is_valid_id($query, "communityid")) return "communityid";
        if (self::is_valid_id($query, "steamid")) return "steamid";
        return "vanity";
        // TODO: Check if vanity URL input is valid
    }

    private function is_valid_id($id, $expected_type)
    {
        switch($expected_type)
        {
            case "steamid":
                if (preg_match("/((?i:STEAM)_)?0:[0-9]:[0-9]*/", $id))
                    return TRUE;
                break;
            case "communityid":
                if (ctype_digit($id) && (strlen($id)==17))
                    return TRUE;
                break;
            default:
                return NULL;
        }
        return FALSE;
    }

    private function convertToSteamID($community_id) {
        if (self::is_valid_id($community_id, "communityid") !== TRUE) throw new WrongIDException($community_id);
        // TODO: Use BCMath maybe
        $temp = intval($community_id) - 76561197960265728;
        $odd_id = $temp % 2;
        $temp = floor($temp / 2);
        return "STEAM_0:".$odd_id.":".$temp;
    }

    private function convertToCommunityID($steam_id) {
        if (self::is_valid_id($steam_id, "steamid") !== TRUE) throw new WrongIDException($steam_id);
        // Example input: STEAM_0:0:17336203 or 0:0:17336203
        // TODO: Use BCMath maybe
        $x = NULL;
        if (preg_match("/(?i:STEAM)_0:[0-9]:[0-9]*/", $steam_id))
            $x = substr($steam_id, 8, 1);
        else if (preg_match("/0:[0-9]:[0-9]*/", $steam_id))
            $x = substr($steam_id, 2, 1);
        else throw new WrongIDException($steam_id);
        $y = substr($steam_id, 4);
        return ($y * 2) + $x + 76561197960265728;
    }

}