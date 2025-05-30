<?php
// Simple debug mode - set to true to see errors, false for production
$debug = true;

if ($debug) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

try {
    // Get database credentials from Laravel's configuration
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    $host = config('database.connections.mysql.host');
    $database = config('database.connections.mysql.database');
    $username = config('database.connections.mysql.username');
    $password = config('database.connections.mysql.password');
    
    // Connect to the database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        if ($debug) {
            error_log("Fetching image for user ID: " . $_GET['id']);
        }
        
        $stmt = $pdo->prepare("SELECT profile FROM users WHERE id = :id");
        $stmt->execute(['id' => $_GET['id']]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && !empty($row['profile'])) {
            if ($debug) {
                error_log("Profile image found for user ID: " . $_GET['id']);
            }
            
            // Detect the image type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($row['profile']);
            
            if ($debug) {
                error_log("Image MIME type: " . $mimeType);
            }
            
            // Set the content type header
            header("Content-Type: $mimeType");
            
            // Cache control (optional)
            header("Cache-Control: public, max-age=3600");
            
            // Output the image data
            echo $row['profile'];
            exit;
        } else {
            if ($debug) {
                error_log("No profile image found for user ID: " . $_GET['id']);
            }
            http_response_code(404);
            echo "Image not found.";
            exit;
        }
    } else {
        http_response_code(400);
        echo "Missing ID.";
        exit;
    }

} catch (PDOException $e) {
    header("HTTP/1.0 500 Internal Server Error");
    exit("Database Error: " . $e->getMessage());
} catch (PDOException $e) {
    if ($debug) {
        error_log("Database Error: " . $e->getMessage());
    }
    header("HTTP/1.0 500 Internal Server Error");
    exit("Database Error: " . ($debug ? $e->getMessage() : "An error occurred while trying to fetch the profile image."));
} catch (Exception $e) {
    if ($debug) {
        error_log("General Error: " . $e->getMessage());
    }
    header("HTTP/1.0 500 Internal Server Error");
    exit("Error: " . ($debug ? $e->getMessage() : "An error occurred while trying to fetch the profile image."));
}
?>
