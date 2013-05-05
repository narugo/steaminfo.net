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
        $interfaces = array();
        foreach ($apis->apilist->interfaces as $interface) {
            array_push($interfaces, new Steam_API_Interface(
                $interface->name, $interface->methods));
        }
        return $apis;
    }

}

class Steam_API_Interface
{

    public $name;
    public $methods = array();

    function __construct($name, $methods = array())
    {
        $this->name = $name;
        foreach ($methods as $method) {
            array_push($this->methods, new Steam_API_Method(
                $method->name, $method->version, $method->httpmethod, $method->parameters));
        }
    }

}

class Steam_API_Method
{

    public $name;
    public $version;
    public $httpmethod;
    public $parameters = array();

    function __construct($name, $version, $httpmethod, $parameters = array())
    {
        $this->name = $name;
        $this->version = $version;
        $this->httpmethod = $httpmethod;
        foreach ($parameters as $param) {
            array_push($this->parameters, new Steam_API_Parameter(
                $param->name, $param->type, $param->optional, $param->description));
        }
    }

}

class Steam_API_Parameter
{

    public $name;
    public $type;
    public $optional;
    public $description;

    function __construct($name, $type, $optional, $description)
    {
        $this->name = $name;
        $this->type = $type;
        $this->optional = $optional;
        $this->description = $description;
    }

}