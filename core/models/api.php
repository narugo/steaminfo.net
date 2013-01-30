<?php

class API_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getAPI()
    {
        $cache_key = 'available_apis';
        $apis = $this->memcached->get($cache_key);
        if ($apis === FALSE) {
            $apis = $this->steam->ISteamWebAPIUtil->GetSupportedAPIList();
            $this->memcached->add($cache_key, $apis, 3000);
        }
        return $apis;
    }

}