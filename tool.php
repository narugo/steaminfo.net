<?php
echo "=== Steam Info command line tool ===\n";

require 'config.php';
require 'vendor/autoload.php'; // Composer autoload
require CORE_DIR . 'application.php';

// Starting main class
$app = new Application(MODE_CMD,$argv);