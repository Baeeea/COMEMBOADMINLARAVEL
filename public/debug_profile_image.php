<?php
// Simple script to debug profile image data in the database

// Get database credentials from Laravel's configuration
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/plain');

// Connect to the database using PDO
try {
    $host = config('database.connections.mysql.host');
    $database = config('database.connections.mysql.database');
    $username = config('database.connections.mysql.username');
    $password = config('database.connections.mysql.password');
    
    echo "Connecting to database: $database on $host as $username\n\n";
    
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get user ID from the request
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        echo "No ID provided. Showing information for all users with profile data.\n\n";
        
        // Query to find all users with profile data
        $stmt = $pdo->query("SELECT id, name, email, LENGTH(profile) as profile_size FROM users WHERE profile IS NOT NULL");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) === 0) {
            echo "No users found with profile data.";
            exit;
        }
        
        echo "Users with profile data:\n";
        foreach ($users as $user) {
            echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Profile Size: {$user['profile_size']} bytes\n";
        }
    } else {
        echo "Retrieving data for user ID: $id\n\n";
        
        // Get user details
        $stmt = $pdo->prepare("SELECT id, name, email, LENGTH(profile) as profile_size, profile FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo "User not found with ID: $id";
            exit;
        }
        
        echo "User Details:\n";
        echo "ID: {$user['id']}\n";
        echo "Name: {$user['name']}\n";
        echo "Email: {$user['email']}\n";
        
        if ($user['profile']) {
            echo "Profile image exists: Yes\n";
            echo "Profile data size: {$user['profile_size']} bytes\n";
            
            // Detect image type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($user['profile']);
            
            echo "Detected MIME Type: $mimeType\n\n";
            
            echo "First 100 bytes of profile data (hexdump):\n";
            echo bin2hex(substr($user['profile'], 0, 50)) . "...\n\n";
            
            echo "To view the actual image, visit: " . url("/profile-image/{$user['id']}") . "\n";
        } else {
            echo "Profile image exists: No\n";
        }
    }
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
