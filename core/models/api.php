<?php

class API_Model extends Model
{

    function __construct()
    {
        parent::__construct();
        $this->steam = new Locomotive();
    }

    public function getAPI()
    {
        return $this->steam->ISteamWebAPIUtil->GetSupportedAPIList();
    }

}