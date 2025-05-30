<?php
// Debug script to check if profile images exist in database
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get database credentials from Laravel's configuration
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $host = config('database.connections.mysql.host');
    $database = config('database.connections.mysql.database');
    $username = config('database.connections.mysql.username');
    $password = config('database.connections.mysql.password');
    
    echo "<h1>Profile Image Check</h1>";
    
    // Connect to the database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Count users with profile images
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE profile IS NOT NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p>Found <strong>{$result['count']}</strong> users with profile images.</p>";
    
    if ($result['count'] > 0) {
        // Get all users with profile images
        $stmt = $pdo->query("SELECT id, name, email, LENGTH(profile) as profile_size FROM users WHERE profile IS NOT NULL");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Profile Size</th><th>Preview</th></tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['profile_size']} bytes</td>";
            echo "<td><img src='/api/profile_image.php?id={$user['id']}' width='50' height='50' style='object-fit: cover;'></td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No users have profile images uploaded yet. Try uploading a profile image first.</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>Database Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
