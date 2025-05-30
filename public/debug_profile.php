<?php
// This file is for debugging profile image issues

// Enable error reporting
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
    
    // Connect to the database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Profile Image Debug</h1>";
    
    if (isset($_GET['id'])) {
        $user_id = $_GET['id'];
        
        // Get user information
        $stmt = $pdo->prepare("SELECT id, name, email, profile FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<h2>User Information</h2>";
            echo "<p><strong>ID:</strong> " . $user['id'] . "</p>";
            echo "<p><strong>Name:</strong> " . $user['name'] . "</p>";
            echo "<p><strong>Email:</strong> " . $user['email'] . "</p>";
            
            if (!empty($user['profile'])) {
                echo "<p><strong>Profile Image:</strong> Image data exists (length: " . strlen($user['profile']) . " bytes)</p>";
                
                // Display image info
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($user['profile']);
                echo "<p><strong>MIME Type:</strong> " . $mimeType . "</p>";
                
                // Show image preview
                echo "<h3>Image Preview:</h3>";
                echo '<img src="get_profile_image.php?id=' . $user_id . '" alt="Profile Image" style="max-width: 200px; border: 1px solid #ddd; padding: 5px;">';
                
                // Show as base64 (alternative method)
                echo "<h3>Base64 Image Preview:</h3>";
                $base64 = base64_encode($user['profile']);
                echo '<img src="data:' . $mimeType . ';base64,' . $base64 . '" alt="Profile Image (Base64)" style="max-width: 200px; border: 1px solid #ddd; padding: 5px;">';
            } else {
                echo "<p><strong>Profile Image:</strong> No image data found</p>";
            }
        } else {
            echo "<p>User not found.</p>";
        }
    } else {
        // List all users with profile images
        $stmt = $pdo->query("SELECT id, name, email, CASE WHEN profile IS NOT NULL THEN 'Yes' ELSE 'No' END as has_image FROM users ORDER BY id");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h2>All Users</h2>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Has Image</th><th>Actions</th></tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['name'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['has_image'] . "</td>";
            echo "<td><a href='?id=" . $user['id'] . "'>View Details</a></td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
