<?php
/**
 * Entry point of the application
 */

define('HOSTNAME', 'steaminfo.net');
define('DEFAULT_PROTOCOL', 'https');
define('WEBSITE_URL', DEFAULT_PROTOCOL . '://' . HOSTNAME . '/');

define('CORE_DIR', 'core/');
define('ASSETS_DIR', 'assets/');
define('PATH_TO_APP', '/home/roman/web/steaminfo.net/');
define('PATH_TO_CORE', PATH_TO_APP . CORE_DIR);
define('PATH_TO_ASSETS', PATH_TO_APP . ASSETS_DIR);

define('ASSETS_DIR_URL', '//' . HOSTNAME . '/' . ASSETS_DIR);

ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

require CORE_DIR . 'config.php';
require CORE_DIR . 'application.php';

// Starting main class
$app = new Application();
