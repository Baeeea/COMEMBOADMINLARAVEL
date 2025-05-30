<?php
// This script validates if profile image data in the database is valid
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database settings
$db_host = '127.0.0.1';
$db_name = 'barangaycomembo';
$db_username = 'root';
$db_password = '';

function getImageInfo($imageData) {
    $info = [];
    
    // Get MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $info['mime_type'] = $finfo->buffer($imageData);
    
    // Get image size in bytes
    $info['size'] = strlen($imageData);
    
    // Attempt to get image dimensions
    $tempFile = tempnam(sys_get_temp_dir(), 'img_');
    file_put_contents($tempFile, $imageData);
    $imgInfo = @getimagesize($tempFile);
    unlink($tempFile);
    
    if ($imgInfo) {
        $info['width'] = $imgInfo[0];
        $info['height'] = $imgInfo[1];
        $info['type'] = $imgInfo[2]; // Image type constant (IMAGETYPE_XXX)
        $info['valid'] = true;
    } else {
        $info['valid'] = false;
    }
    
    return $info;
}

// CSS styles for the page
echo '<!DOCTYPE html>
<html>
<head>
    <title>Profile Image Validation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #333; }
        .card { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .card.valid { background-color: #e8f5e9; }
        .card.invalid { background-color: #ffebee; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        .preview { max-width: 200px; max-height: 200px; border: 1px solid #ddd; }
        .base64-preview { max-width: 200px; max-height: 200px; border: 1px solid #ddd; margin-top: 10px; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; max-height: 100px; }
        .hex-dump { font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <h1>Profile Image Validation Tool</h1>';

try {
    // Connect to database
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Get specific user or all users
    $userId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    if ($userId) {
        $stmt = $pdo->prepare("SELECT id, name, email, profile FROM users WHERE id = ?");
        $stmt->execute([$userId]);
    } else {
        $stmt = $pdo->query("SELECT id, name, email, profile FROM users WHERE profile IS NOT NULL");
    }
    
    $users = $stmt->fetchAll();
    
    if (count($users) == 0) {
        echo "<p>No users found with profile images.</p>";
    } else {
        echo "<p>Found " . count($users) . " user(s) with profile data.</p>";
        
        foreach ($users as $user) {
            echo "<div class='card " . (!empty($user['profile']) ? 'valid' : 'invalid') . "'>";
            echo "<h2>User: {$user['name']} (ID: {$user['id']})</h2>";
            
            if (!empty($user['profile'])) {
                $imageInfo = getImageInfo($user['profile']);
                
                echo "<table>";
                echo "<tr><th>Email:</th><td>{$user['email']}</td></tr>";
                echo "<tr><th>Image Size:</th><td>{$imageInfo['size']} bytes</td></tr>";
                echo "<tr><th>MIME Type:</th><td>{$imageInfo['mime_type']}</td></tr>";
                
                if ($imageInfo['valid']) {
                    echo "<tr><th>Dimensions:</th><td>{$imageInfo['width']}x{$imageInfo['height']} pixels</td></tr>";
                    echo "<tr><th>Image Type:</th><td>" . image_type_to_mime_type($imageInfo['type']) . "</td></tr>";
                    echo "<tr><th>Valid Image:</th><td>Yes ✅</td></tr>";
                } else {
                    echo "<tr><th>Valid Image:</th><td>No ❌</td></tr>";
                }
                echo "</table>";
                
                // Show direct URL
                echo "<p>Direct image URL: <a href='/direct_image_test.php?id={$user['id']}' target='_blank'>/direct_image_test.php?id={$user['id']}</a></p>";
                
                // Display image preview
                echo "<h3>Direct Image Preview:</h3>";
                echo "<img src='/direct_image_test.php?id={$user['id']}' class='preview' alt='User profile preview'>";
                
                // Display base64 encoded version (alternative method)
                $base64 = base64_encode($user['profile']);
                echo "<h3>Base64 Image Preview:</h3>";
                echo "<img src='data:{$imageInfo['mime_type']};base64,{$base64}' class='base64-preview' alt='Base64 preview'>";
                
                // Show first 100 bytes as hex dump to help diagnose corruption
                $hexDump = bin2hex(substr($user['profile'], 0, 100));
                echo "<h3>First 100 Bytes (Hex):</h3>";
                echo "<pre class='hex-dump'>";
                $formatted = '';
                for($i = 0; $i < strlen($hexDump); $i += 2) {
                    $formatted .= substr($hexDump, $i, 2) . ' ';
                    if(($i+2) % 32 == 0) $formatted .= "\n";
                }
                echo htmlspecialchars($formatted);
                echo "</pre>";
                
                // Show HTML img tag example
                echo "<h3>HTML Code Example:</h3>";
                echo "<pre>";
                echo htmlspecialchars("<img src=\"/direct_image_test.php?id={$user['id']}\" alt=\"User Profile\" width=\"100\" height=\"100\">");
                echo "</pre>";
                
            } else {
                echo "<p>No profile data available for this user.</p>";
            }
            
            echo "</div>";
        }
    }
    
} catch (PDOException $e) {
    echo "<div class='card invalid'>";
    echo "<h2>Database Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='card invalid'>";
    echo "<h2>Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo '</body></html>';
?>
