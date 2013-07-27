<?php
/**
 * Main application configuration file
 */

/**
 * Database configuration
 */
define('DB_DRIVER', 'pdo_pgsql');
define('DB_HOST', 'localhost');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_NAME', '');

/**
 * Memcached configuration
 */
define('MEMCACHED_SERVER', 'localhost');
define('MEMCACHED_PORT', 11211);

/**
 * Error reporting
 */
ini_set('error_reporting', E_ALL);
if (isset($_SERVER['REMOTE_ADDR']))
    if ($_SERVER['REMOTE_ADDR'] == '192.168.1.1')
        ini_set('display_errors', 1);

define('DEFAULT_LOG_FILE', '/home/user/steaminfo.net/main.log');

define('HOSTNAME', 'steaminfo.net');
define('DEFAULT_PROTOCOL', 'https');

define('WEBSITE_URL', DEFAULT_PROTOCOL . '://' . HOSTNAME . '/');

define('CORE_DIR', 'core/');
define('STATIC_DIR', 'static/');
define('PATH_TO_APP', __DIR__ . '/');
define('PATH_TO_CORE', PATH_TO_APP . CORE_DIR);
define('PATH_TO_ASSETS', PATH_TO_APP . STATIC_DIR);

define('STATIC_DIR_URL', '//' . HOSTNAME . '/' . STATIC_DIR);

/**
 * Steam
 */
define('STEAM_API_KEY', '');
