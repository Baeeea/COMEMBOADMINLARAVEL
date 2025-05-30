<?php
// This is a direct image test file without any framework dependencies
$debug = true;

if ($debug) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Database settings - update with your actual values
$db_host = '127.0.0.1';
$db_name = 'barangaycomembo';
$db_username = 'root';
$db_password = '';

// Get the user ID from the query parameter
$userId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// If no user ID provided, display a form for input
if (is_null($userId)) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Direct Profile Image Test</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
        </style>
    </head>
    <body>
        <h1>Direct Profile Image Test</h1>
        <form>
            <label for="id">Enter User ID:</label>
            <input type="number" name="id" id="id" required>
            <button type="submit">Get Profile Image</button>
        </form>
    </body>
    </html>
    <?php
    exit;
}

try {
    // Create PDO connection
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
      // Log request info
    if ($debug) {
        error_log("Direct Image Test: Fetching image for user ID: $userId");
    }
    
    // Query for the user's profile image
    $stmt = $pdo->prepare("SELECT id, name, email, profile FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    
    if ($debug) {
        if ($result) {
            error_log("User found: ID={$result['id']}, Name={$result['name']}");
            error_log("Profile data exists: " . (isset($result['profile']) ? "Yes" : "No"));
            if (isset($result['profile'])) {
                error_log("Profile data length: " . strlen($result['profile']) . " bytes");
            }
        } else {
            error_log("No user found with ID: $userId");
        }
    }
    
    if ($result && !empty($result['profile'])) {
        // We have image data
        $imageData = $result['profile'];
        
        // Try to detect the MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
          if ($debug) {
            error_log("Image MIME type detected: $mimeType");
        }
        
        // Output image with appropriate headers
        header("Content-Type: $mimeType");
        header("Cache-Control: no-cache, no-store, must-revalidate"); // No caching
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // Force the output as binary data
        if (ob_get_level()) ob_end_clean(); // Clean any output buffers
        echo $imageData;
    } else {
        // No image found
        header("HTTP/1.0 404 Not Found");
        echo "No profile image found for user ID: $userId";
    }
    
} catch (PDOException $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo "Database error: " . ($debug ? $e->getMessage() : "A database error occurred.");
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo "Error: " . ($debug ? $e->getMessage() : "An error occurred.");
}
?>
