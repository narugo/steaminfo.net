<?php
/**
 * Entry point of the application
 */

require 'config.php';
require 'vendor/autoload.php'; // Composer autoload
require CORE_DIR . 'application.php';

// Starting main class
$app = new Application();
