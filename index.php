<?php
/**
 * Entry point of the application
 */

require 'core/config.php';
require 'core/application.php';

// Setting error reporting level
error_reporting(E_ALL ^ E_NOTICE);

// Starting main class
$app = new Application();
