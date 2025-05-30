<?php
// Set error reporting (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Get and validate the ID parameter
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $table = isset($_GET['type']) && $_GET['type'] === 'resident' ? 'residents' : 'users';
    
    try {
        $stmt = $pdo->prepare("SELECT profile FROM $table WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && $row['profile']) {
            // Try to detect the image MIME type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($row['profile']);
            
            // Set appropriate headers
            header("Content-Type: $mimeType");
            header("Cache-Control: public, max-age=86400"); // Cache for one day
            
            // Output the image data
            echo $row['profile'];
        } else {
            http_response_code(404);
            echo "Profile image not found.";
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Database error: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "Invalid or missing ID.";
}
