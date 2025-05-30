<?php
/**
 * Profile Image API
 * 
 * This script provides both backward compatibility with the old direct API
 * and will continue to work while also supporting the new Laravel route.
 * 
 * If the REDIRECT_TO_LARAVEL_ROUTE constant is set to true, it will redirect 
 * requests to the new Laravel route. Otherwise, it will handle the request directly.
 */

// Whether to redirect to the Laravel route or handle directly
define('REDIRECT_TO_LARAVEL_ROUTE', false);

// Set error reporting (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set CORS headers for RESTful API access
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if we should redirect to the Laravel route
if (REDIRECT_TO_LARAVEL_ROUTE) {
    // Get the ID parameter
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$id) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Missing or invalid ID parameter']);
        exit;
    }

    // Get optional parameters
    $debug = isset($_GET['debug']) ? $_GET['debug'] : '0';
    $size = isset($_GET['size']) ? $_GET['size'] : '150';
    $t = isset($_GET['t']) ? $_GET['t'] : time(); // Cache buster
    $v = isset($_GET['v']) ? $_GET['v'] : $t;     // Version parameter

    // Build the new URL with Laravel's route
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $newUrl = "$protocol://$host/profile-image/$id?debug=$debug&size=$size&v=$v";

    // Redirect to the new URL
    header("Location: $newUrl", true, 302);
    exit;
}

// Continue with the original implementation for backward compatibility
// Get database configuration from Laravel's .env file
$envPath = dirname(dirname(__DIR__)) . '/.env';
$dbConfig = [];

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $dbConfig[trim($key)] = trim($value);
        }
    }
}

// Set database connection parameters
$host = $dbConfig['DB_HOST'] ?? 'localhost';
$database = $dbConfig['DB_DATABASE'] ?? 'laravel';
$username = $dbConfig['DB_USERNAME'] ?? 'root';
$password = $dbConfig['DB_PASSWORD'] ?? '';

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

// Debug mode
$debug = isset($_GET['debug']) && $_GET['debug'] == '1';

// Get and validate the ID parameter
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Determine which table to query based on the type parameter
    if (isset($_GET['type'])) {
        if ($_GET['type'] === 'resident') {
            $table = 'residents';
        } else if ($_GET['type'] === 'admin') {
            $table = 'admins';
        } else {
            $table = 'users';
        }
    } else {
        $table = 'users'; // Default to users table
    }
    
    try {
        if ($debug) {
            error_log("Profile API: Fetching image for $table ID: $id");
        }
        
        $stmt = $pdo->prepare("SELECT id, name, profile FROM $table WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($debug && $row) {
            error_log("Found user: ID={$row['id']}, Name={$row['name']}");
            error_log("Profile data exists: " . (!empty($row['profile']) ? "Yes (".strlen($row['profile'])." bytes)" : "No"));
        }
          if ($row && !empty($row['profile'])) {
            // Try to detect the image MIME type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($row['profile']);
            
            if ($debug) {
                error_log("Image MIME type detected: $mimeType");
            }
            
            // Generate ETag for client-side caching
            $etag = md5($row['profile']);
            $last_modified = gmdate('D, d M Y H:i:s', time()) . ' GMT';
            
            // Check if browser cache is still valid
            if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
            
            // Set appropriate headers
            header("Content-Type: $mimeType");
            header("ETag: $etag");
            header("Last-Modified: $last_modified");
            
            if (!$debug) {
                // Enable caching for production
                header("Cache-Control: public, max-age=86400"); // Cache for 1 day
            } else {
                // Disable caching for debugging
                header("Cache-Control: no-cache, no-store, must-revalidate");
                header("Pragma: no-cache");
                header("Expires: 0");
            }
            
            // Clean any output buffers
            if (ob_get_level()) ob_end_clean();
            
            // Output the image data
            echo $row['profile'];
        } else {
            if ($debug) {
                http_response_code(404);
                echo "Profile image not found for $table ID: $id";
                if ($row) {
                    echo "<br>User exists but has no profile image data.";
                } else {
                    echo "<br>User not found.";
                }
            } else {
                http_response_code(404);
                echo "Profile image not found.";
            }
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Database error: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "Invalid or missing ID.";
}
