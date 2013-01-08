<?php
/**
 * Entry point of the application
 */

define('PATH_TO_CORE', 'core/');
define('PATH_TO_ASSETS', '//' . $_SERVER['HTTP_HOST'] . '/assets/');

require PATH_TO_CORE . 'config.php';
require PATH_TO_CORE . 'application.php';

ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

// Starting main class
$app = new Application();
