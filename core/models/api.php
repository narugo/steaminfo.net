<?php

use SteamInfo\Models\Entities\ApiInterface;

class API_Model extends Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getInterfaces()
    {
        $cache_key = 'api_interfaces';
        $interfaces = $this->memcached->get($cache_key);
        if ($interfaces === FALSE) {
            self::updateInterfaces();
            $apiRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\ApiInterface');
            $interfaces = $apiRepository->findAll();
            $this->memcached->add($cache_key, $interfaces, 3000);
        }
        return $interfaces;
    }

    private function updateInterfaces()
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('api_interface', true));
        $connection->executeUpdate($platform->getTruncateTableSQL('api_method', true));
        $connection->executeUpdate($platform->getTruncateTableSQL('api_method_parameter', true));

        $result = $this->steam->ISteamWebAPIUtil->GetSupportedAPIList();
        foreach ($result->apilist->interfaces as $interface) {
            $current_interface = new ApiInterface($interface->name);
            $this->entityManager->persist($current_interface);
            foreach ($interface->methods as $method) {
                $current_interface->addMethods($method->name, $method->version, $method->httpmethod);
                foreach ($method->parameters as $parameter) {
                    // TODO: Fix
                    /* $current_parameter = new ApiMethodParameter(
                         $parameter->name,
                         $current_method->getName(),
                         $current_method->getVersion(),
                         $current_interface->getName()
                     );
                     $this->entityManager->persist($current_parameter);
                     $current_parameter->setOptional($parameter->optional);
                     $current_parameter->setType($parameter->type);
                     if (!empty($parameter->description)) $current_parameter->setDescription($parameter->description);  */
                }
            }
            $this->entityManager->flush();
        }
        $this->entityManager->flush();
    }

    public function getInterface($name)
    {
        $cache_key = 'api_interface_' . $name;
        $interface = $this->memcached->get($cache_key);
        if ($interface === FALSE) {
            $apiRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\ApiInterface');
            $interface = $apiRepository->find($name);
            $this->memcached->add($cache_key, $interface, 3000);
        }
        return $interface;
    }

    public function getMethod($name)
    {
        $cache_key = 'api_method_' . $name;
        $interface = $this->memcached->get($cache_key);
        if ($interface === FALSE) {
            $apiRepository = $this->entityManager->getRepository('SteamInfo\Models\Entities\ApiMethod');
            $interface = $apiRepository->find($name);
            $this->memcached->add($cache_key, $interface, 3000);
        }
        return $interface;
    }

}