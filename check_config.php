<?php

echo "Testing Laravel database configuration...\n";

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    // Get database config
    $config = $app['config'];
    
    echo "DB_CONNECTION from env: " . env('DB_CONNECTION') . "\n";
    echo "Default connection: " . $config->get('database.default') . "\n";
    echo "MySQL config: " . json_encode($config->get('database.connections.mysql')) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
