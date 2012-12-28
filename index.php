<?php
/**
 * Entry point of the application
 */

define('PATH_TO_CORE', 'core/');
define('PATH_TO_ASSETS', '//' . $_SERVER['HTTP_HOST'] . '/assets/');

require PATH_TO_CORE . 'config.php';
require PATH_TO_CORE . 'application.php';

// Setting error reporting level
error_reporting(E_ALL ^ E_NOTICE);

// Starting main class
$app = new Application();
