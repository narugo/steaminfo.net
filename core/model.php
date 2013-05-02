<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

define('PATH_TO_MODELS', CORE_DIR . 'models/');
define('PATH_TO_METADATA', PATH_TO_MODELS . 'entities/');

/**
 * Statuses
 */
define('STATUS_OUTDATED', 'outdated');
define('STATUS_SUCCESS', 'success');
define('STATUS_UNAUTHORIZED', 'unauthorized');
define('STATUS_API_UNAVAILABLE', 'private');
define('STATUS_UNKNOWN', 'unknown');

class Model
{

    function __construct()
    {
        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(PATH_TO_METADATA), $isDevMode);
        $connection = array(
            'driver' => DB_DRIVER,
            'user' => DB_USERNAME,
            'password' => DB_PASSWORD,
            'dbname' => DB_NAME . '_test',
        );
        $this->entityManager = EntityManager::create($connection, $config);

        $this->db = new Database();

        $this->memcached = new Memcached();
        $this->memcached->addServer(MEMCACHED_SERVER, MEMCACHED_PORT);

        $this->steam = new Locomotive(STEAM_API_KEY);
    }

}