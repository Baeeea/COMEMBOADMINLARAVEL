<?php
// Standalone profile image API that doesn't rely on Laravel's framework
// Set to true to see detailed errors, set to false in production
$debug = true;

if ($debug) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Database connection settings - update these with your actual values
$db_host = 'localhost'; 
$db_name = 'barangaycomembo';
$db_user = 'root';
$db_pass = '';

// Get user id from the query string
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    exit("Invalid or missing user ID");
}

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query for profile image
    $stmt = $pdo->prepare("SELECT profile FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && !empty($user['profile'])) {
        // Get image data
        $imageData = $user['profile'];
        
        // Try to detect image type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
        
        if ($debug) {
            error_log("User ID: $id - Image found with MIME type: $mimeType");
        }
        
        // Set headers and output image
        header("Content-Type: $mimeType");
        header("Cache-Control: public, max-age=3600");
        echo $imageData;
        exit;
    } else {
        if ($debug) {
            error_log("User ID: $id - No image found or image is empty");
        }
        http_response_code(404);
        exit("No profile image found for this user");
    }
} catch (PDOException $e) {
    if ($debug) {
        error_log("Database Error: " . $e->getMessage());
    }
    http_response_code(500);
    exit($debug ? "Database Error: " . $e->getMessage() : "A database error occurred");
} catch (Exception $e) {
    if ($debug) {
        error_log("Error: " . $e->getMessage());
    }
    http_response_code(500);
    exit($debug ? "Error: " . $e->getMessage() : "An error occurred");
}
?>
