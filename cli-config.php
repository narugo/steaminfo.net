<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

require_once 'config.php';

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/core/models/entities"), $isDevMode);
$connection = array(
    'driver' => DB_DRIVER,
    'user' => DB_USERNAME,
    'password' => DB_PASSWORD,
    'dbname' => DB_NAME,
);
$entityManager = EntityManager::create($connection, $config);

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
));