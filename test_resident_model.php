<?php

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Test if we can access the Resident model
    $residentClass = \App\Models\Resident::class;
    echo "✅ Resident model class found: $residentClass\n";
    
    // Test database connection and table
    $count = \App\Models\Resident::count();
    echo "✅ Residents table accessible. Total records: $count\n";
    
    // Test fetching a sample record
    $firstResident = \App\Models\Resident::first();
    if ($firstResident) {
        echo "✅ Sample resident found:\n";
        echo "   ID: " . $firstResident->user_id . "\n";
        echo "   Name: " . $firstResident->firstname . " " . $firstResident->lastname . "\n";
        echo "   Email: " . $firstResident->email . "\n";
        echo "   Username: " . $firstResident->username . "\n";
    } else {
        echo "⚠️ No residents found in the table\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
