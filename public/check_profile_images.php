<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get database credentials from Laravel's configuration
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get database connection settings from Laravel config
$db_host = config('database.connections.mysql.host');
$db_name = config('database.connections.mysql.database');
$db_user = config('database.connections.mysql.username');
$db_pass = config('database.connections.mysql.password');

echo "<html><head><title>Profile Image Check</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1, h2 { color: #333; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .status-ok { color: green; }
    .status-error { color: red; }
    </style>";
echo "</head><body>";
echo "<h1>Profile Image Diagnostic Tool</h1>";

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p class='status-ok'>✅ Successfully connected to database: $db_name</p>";
    
    // Check if the users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='status-ok'>✅ 'users' table exists</p>";
        
        // Check if the profile column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile'");
        if ($stmt->rowCount() > 0) {
            $column = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p class='status-ok'>✅ 'profile' column exists in 'users' table (Type: {$column['Type']})</p>";
        } else {
            echo "<p class='status-error'>❌ 'profile' column does NOT exist in 'users' table</p>";
        }
        
        // Get users with profile images
        $stmt = $pdo->query("SELECT id, name, email, 
                             IF(profile IS NULL, 'NULL', 
                                IF(LENGTH(profile) > 0, CONCAT(LENGTH(profile), ' bytes'), 'Empty')) as profile_size 
                             FROM users 
                             ORDER BY id");
        
        echo "<h2>Users in Database</h2>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Profile Image</th><th>Actions</th></tr>";
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['email']}</td>";
            
            if ($row['profile_size'] == 'NULL') {
                echo "<td class='status-error'>No image data (NULL)</td>";
                $hasImage = false;
            } elseif ($row['profile_size'] == 'Empty') {
                echo "<td class='status-error'>Empty image data</td>";
                $hasImage = false;
            } else {
                echo "<td class='status-ok'>{$row['profile_size']}</td>";
                $hasImage = true;
            }
            
            echo "<td>";
            if ($hasImage) {
                echo "<a href='/profile_api.php?id={$row['id']}' target='_blank'>View Image</a> | ";
                echo "<img src='/profile_api.php?id={$row['id']}' height='30' width='30' style='border-radius: 50%;'>";
            } else {
                echo "No image available";
            }
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
    } else {
        echo "<p class='status-error'>❌ 'users' table does NOT exist</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='status-error'>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p class='status-error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>
