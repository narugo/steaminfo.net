<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

define('PATH_TO_MODELS', CORE_DIR . 'models/');
define('PATH_TO_ENTITIES', PATH_TO_MODELS . 'entities/');

foreach (glob(PATH_TO_ENTITIES . '*.php') as $entity) {
    require $entity;
}

class Model
{

    function __construct()
    {
        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(PATH_TO_ENTITIES), $isDevMode);
        $connection = array(
            'driver' => DB_DRIVER,
            'user' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'dbname' => DB_NAME,
        );
        $this->entityManager = EntityManager::create($connection, $config);
        $this->helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
            'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($this->entityManager)
        ));

        $this->memcached = new Memcached();
        $this->memcached->addServer(MEMCACHED_SERVER, MEMCACHED_PORT);

        $this->steam = new Locomotive(STEAM_API_KEY);
    }

}