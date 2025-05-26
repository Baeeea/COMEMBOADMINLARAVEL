<?php
// Simple script to test database connectivity and messaging functionality

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Set up basic response headers
header('Content-Type: application/json');

try {
    // Test database connection
    $dbConnection = DB::connection()->getPdo();
    echo json_encode([
        "status" => "success",
        "message" => "Database connection successful: " . DB::connection()->getDatabaseName(),
        "php_version" => PHP_VERSION,
        "laravel_version" => app()->version(),
        "user_count" => \App\Models\User::count(),
        "message_count" => \App\Models\Message::count(),
        "environment" => app()->environment(),
        "debug_mode" => config('app.debug'),
        "messages_table_exists" => \Illuminate\Support\Facades\Schema::hasTable('messages'),
        "users_table_exists" => \Illuminate\Support\Facades\Schema::hasTable('users'),
    ]);
} catch (\Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine(),
        "trace" => $e->getTraceAsString(),
    ]);
}
