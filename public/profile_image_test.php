<?php
// Profile Image Test - Comprehensive testing and diagnostic tool
// This script tests all aspects of profile image functionality

// Enable full error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get Laravel environment
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Add page styling
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Image Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .results-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .test-result {
            padding: 15px;
            margin-bottom: 10px;
            border-left: 5px solid #e9ecef;
        }
        .test-success {
            border-left-color: #28a745;
            background-color: #d4edda;
        }
        .test-warning {
            border-left-color: #ffc107;
            background-color: #fff3cd;
        }
        .test-error {
            border-left-color: #dc3545;
            background-color: #f8d7da;
        }
        .test-info {
            border-left-color: #17a2b8;
            background-color: #d1ecf1;
        }
        pre {
            background: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            overflow-x: auto;
        }
        code {
            font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
            background: #f4f4f4;
            padding: 2px 4px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table, th, td {
            border: 1px solid #dee2e6;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .image-preview {
            max-width: 150px;
            max-height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: block;
            margin: 10px 0;
        }
        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 10px;
            color: #fff;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .badge-info {
            background-color: #17a2b8;
        }
        .flex-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .flex-item {
            flex: 1;
            min-width: 300px;
        }
        .nav-tabs {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            border-bottom: 1px solid #dee2e6;
        }
        .nav-tabs li {
            margin-bottom: -1px;
        }
        .nav-tabs a {
            display: block;
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: #495057;
            border: 1px solid transparent;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }
        .nav-tabs a.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        .tab-content {
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: 0;
        }
        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block;
        }
    </style>
</head>
<body>
    <h1>Profile Image System Test</h1>
    <p>This tool tests and diagnoses issues with the profile image system.</p>';

// ===== DATABASE TESTS =====
echo '<div class="card">
    <h2>1. Database Tests</h2>';

try {
    // Create database connection
    $db = config('database.connections.mysql');
    $pdo = new PDO(
        "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
        $db['username'],
        $db['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo '<div class="test-result test-success">
        <strong>✓ Database connection successful</strong>
        <p>Connected to ' . htmlspecialchars($db['database']) . ' on ' . htmlspecialchars($db['host']) . '</p>
    </div>';
    
    // Check users table structure
    $stmt = $pdo->query("SHOW CREATE TABLE users");
    $tableInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    $profileColumnExists = false;
    $profileColumnType = "";
    
    // Check if the profile column exists and its type
    if (preg_match('/`profile`\s+([^\s,]+)/', $tableInfo['Create Table'], $matches)) {
        $profileColumnExists = true;
        $profileColumnType = $matches[1];
        
        if (strtolower($profileColumnType) === 'longblob') {
            echo '<div class="test-result test-success">
                <strong>✓ Profile column has correct type</strong>
                <p>The profile column is defined as ' . htmlspecialchars($profileColumnType) . ', which is suitable for storing binary image data.</p>
            </div>';
        } else {
            echo '<div class="test-result test-warning">
                <strong>⚠ Profile column might have incorrect type</strong>
                <p>The profile column is defined as ' . htmlspecialchars($profileColumnType) . ', but LONGBLOB is recommended for storing binary image data.</p>
                <p>Consider altering the table with:</p>
                <pre>ALTER TABLE users MODIFY COLUMN profile LONGBLOB;</pre>
            </div>';
        }
    } else {
        echo '<div class="test-result test-error">
            <strong>✗ Profile column not found</strong>
            <p>The users table does not have a profile column. This is required for storing profile images.</p>
            <p>Consider adding the column with:</p>
            <pre>ALTER TABLE users ADD COLUMN profile LONGBLOB;</pre>
        </div>';
    }
    
    // Check if users have profile images
    $stmt = $pdo->query("SELECT COUNT(*) as total, COUNT(profile) as with_profile FROM users");
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($counts['total'] == 0) {
        echo '<div class="test-result test-warning">
            <strong>⚠ No users found</strong>
            <p>There are no users in the database to test profile images with.</p>
        </div>';
    } else if ($counts['with_profile'] == 0) {
        echo '<div class="test-result test-warning">
            <strong>⚠ No profile images found</strong>
            <p>There are ' . $counts['total'] . ' users in the database, but none have profile images stored.</p>
        </div>';
    } else {
        $percentage = round(($counts['with_profile'] / $counts['total']) * 100, 1);
        echo '<div class="test-result test-success">
            <strong>✓ Profile images found</strong>
            <p>' . $counts['with_profile'] . ' out of ' . $counts['total'] . ' users (' . $percentage . '%) have profile images stored.</p>
        </div>';
        
        // Get sample of users with profile images
        $stmt = $pdo->query("SELECT id, name, email, LENGTH(profile) as profile_size FROM users WHERE profile IS NOT NULL LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo '<table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Profile Size</th>
            </tr>';
        
        foreach ($users as $user) {
            echo '<tr>
                <td>' . $user['id'] . '</td>
                <td>' . htmlspecialchars($user['name']) . '</td>
                <td>' . htmlspecialchars($user['email']) . '</td>
                <td>' . round($user['profile_size'] / 1024, 1) . ' KB</td>
            </tr>';
        }
        
        echo '</table>';
    }
} catch (PDOException $e) {
    echo '<div class="test-result test-error">
        <strong>✗ Database error</strong>
        <p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>
    </div>';
}

echo '</div>'; // End database tests

// ===== API TESTS =====
echo '<div class="card">
    <h2>2. API Tests</h2>';

// Check if profile_image.php exists
if (file_exists(__DIR__ . '/api/profile_image.php')) {
    echo '<div class="test-result test-success">
        <strong>✓ API endpoint exists</strong>
        <p>The profile_image.php API endpoint was found at the expected location.</p>
    </div>';
    
    // Test API with sample user if available
    if (isset($users) && !empty($users)) {
        $testUser = $users[0];
        $apiUrl = '/api/profile_image.php?id=' . $testUser['id'] . '&t=' . time();
        
        echo '<div class="test-result test-info">
            <strong>API Test with User ID ' . $testUser['id'] . '</strong>
            <p>Testing API endpoint with a user that has a profile image:</p>
            <p><code>' . htmlspecialchars($apiUrl) . '</code></p>
            <div>
                <strong>Image from API:</strong><br>
                <img src="' . $apiUrl . '" class="image-preview" alt="Profile from API" onerror="this.src=\'https://ui-avatars.com/api/?name=' . urlencode($testUser['name']) . '&color=7F9CF5&background=EBF4FF&size=150\'; this.onerror=null; document.getElementById(\'api-error-message\').style.display=\'block\';">
                <div id="api-error-message" style="display:none;" class="test-result test-error">
                    <strong>✗ API image failed to load</strong>
                    <p>The image could not be loaded from the API. This could be due to an error in the API or invalid image data.</p>
                </div>
            </div>
            <p>Direct API URL: <a href="' . $apiUrl . '" target="_blank">' . htmlspecialchars($apiUrl) . '</a></p>
        </div>';
        
        // Test with debug mode
        echo '<div class="test-result test-info">
            <strong>API Debug Mode</strong>
            <p>You can add &debug=1 parameter to see detailed debug information:</p>
            <p><a href="/api/profile_image.php?id=' . $testUser['id'] . '&debug=1" target="_blank">/api/profile_image.php?id=' . $testUser['id'] . '&debug=1</a></p>
        </div>';
    }
} else {
    echo '<div class="test-result test-error">
        <strong>✗ API endpoint missing</strong>
        <p>The profile_image.php API endpoint was not found at the expected location (/api/profile_image.php).</p>
        <p>This file is required to serve profile images from the database.</p>
    </div>';
}

echo '</div>'; // End API tests

// ===== MODEL TESTS =====
echo '<div class="card">
    <h2>3. Model Tests</h2>';

try {
    // Test Admin model
    $adminClass = 'App\\Models\\Admin';
    if (class_exists($adminClass)) {
        echo '<div class="test-result test-success">
            <strong>✓ Admin model exists</strong>
            <p>The Admin model class was found.</p>
        </div>';
        
        // Check if Admin model has getAvatarUrl method
        $reflection = new ReflectionClass($adminClass);
        if ($reflection->hasMethod('getAvatarUrl')) {
            echo '<div class="test-result test-success">
                <strong>✓ getAvatarUrl method exists</strong>
                <p>The Admin model has the getAvatarUrl method required for profile images.</p>
            </div>';
        } else {
            echo '<div class="test-result test-error">
                <strong>✗ getAvatarUrl method missing</strong>
                <p>The Admin model does not have the getAvatarUrl method which is needed for profile images.</p>
            </div>';
        }
        
        // Test with an actual admin user
        $admin = \App\Models\Admin::where('role', 'admin')->first();
        if ($admin) {
            echo '<div class="test-result test-info">
                <strong>Admin User Test</strong>
                <p>Testing with admin: ' . htmlspecialchars($admin->name) . ' (ID: ' . $admin->id . ')</p>';
                
            if (method_exists($admin, 'getAvatarUrl')) {
                $avatarUrl = $admin->getAvatarUrl();
                echo '<p>Avatar URL from getAvatarUrl(): <code>' . htmlspecialchars($avatarUrl) . '</code></p>';
            }
            
            echo '<p>Direct API URL: <code>/api/profile_image.php?id=' . $admin->id . '</code></p>';
            
            if ($admin->profile) {
                echo '<p><span class="badge badge-success">Has profile image</span> Profile image size: ' . round(strlen($admin->profile) / 1024, 1) . ' KB</p>';
            } else {
                echo '<p><span class="badge badge-warning">No profile image</span> This admin does not have a profile image stored.</p>';
            }
            
            echo '<div>
                <strong>Image from API:</strong><br>
                <img src="/api/profile_image.php?id=' . $admin->id . '&t=' . time() . '" class="image-preview" alt="Admin Profile" onerror="this.src=\'https://ui-avatars.com/api/?name=' . urlencode($admin->name) . '&color=7F9CF5&background=EBF4FF&size=150\';">
            </div>';
            
            echo '</div>';
        } else {
            echo '<div class="test-result test-warning">
                <strong>⚠ No admin users found</strong>
                <p>Could not find any users with admin role for testing.</p>
            </div>';
        }
    } else {
        echo '<div class="test-result test-error">
            <strong>✗ Admin model not found</strong>
            <p>The Admin model class does not exist in the expected location.</p>
        </div>';
    }
} catch (Exception $e) {
    echo '<div class="test-result test-error">
        <strong>✗ Error testing models</strong>
        <p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>
    </div>';
}

echo '</div>'; // End model tests

// ===== TEMPLATE TESTS =====
echo '<div class="card">
    <h2>4. Template Tests</h2>';

$templatesWithProfileImages = [
    'admin.blade.php',
    'viewadmin.blade.php'
];

foreach ($templatesWithProfileImages as $template) {
    $templatePath = base_path() . '/resources/views/' . $template;
    if (file_exists($templatePath)) {
        $content = file_get_contents($templatePath);
        $hasProfileCheck = strpos($content, 'Auth::user()->profile') !== false;
        $hasApiUrl = strpos($content, '/api/profile_image.php') !== false;
        $hasAvatarMethod = strpos($content, 'getAvatarUrl') !== false;
        
        echo '<div class="test-result ' . ($hasProfileCheck && $hasApiUrl ? 'test-success' : 'test-warning') . '">
            <strong>' . ($hasProfileCheck && $hasApiUrl ? '✓' : '⚠') . ' ' . htmlspecialchars($template) . '</strong>';
        
        if ($hasProfileCheck && $hasApiUrl) {
            echo '<p>This template correctly checks for profile images and uses the API URL.</p>';
        } else if ($hasAvatarMethod) {
            echo '<p>This template uses the getAvatarUrl method, which should work if implemented correctly.</p>';
        } else {
            echo '<p>This template might not be properly set up for profile images.</p>';
        }
        
        // Extract image tag code samples
        if (preg_match_all('/<img[^>]*src="[^"]*(?:profile_image\.php|getAvatarUrl)[^"]*"[^>]*>/', $content, $matches)) {
            echo '<p>Found ' . count($matches[0]) . ' profile image tags:</p>';
            echo '<pre>';
            foreach ($matches[0] as $match) {
                echo htmlspecialchars($match) . "\n\n";
            }
            echo '</pre>';
        }
        
        echo '</div>';
    } else {
        echo '<div class="test-result test-warning">
            <strong>⚠ ' . htmlspecialchars($template) . ' not found</strong>
            <p>This template file does not exist at the expected location.</p>
        </div>';
    }
}

echo '</div>'; // End template tests

// ===== CODE SAMPLES AND INSTRUCTIONS =====
echo '<div class="card">
    <h2>5. Code Samples & Instructions</h2>
    
    <div class="flex-container">
        <div class="flex-item">
            <h3>Displaying Profile Image</h3>
            <pre>&lt;img src="{{ Auth::user()->profile ? 
    url(\'/api/profile_image.php?id=\'.Auth::user()->id.\'.&t=\'.time()) : 
    \'https://ui-avatars.com/api/?name=\'.urlencode(Auth::user()->name).\'.&color=7F9CF5&background=EBF4FF&size=30\' 
}}" alt="Profile" class="rounded-circle"&gt;</pre>
        </div>
        
        <div class="flex-item">
            <h3>Upload Form</h3>
            <pre>&lt;form action="{{ route(\'admin.update\', $admin->id) }}" method="POST" enctype="multipart/form-data"&gt;
    @csrf
    @method(\'PUT\')
    
    &lt;input type="file" name="profile" accept="image/*"&gt;
    
    &lt;button type="submit"&gt;Upload&lt;/button&gt;
&lt;/form&gt;</pre>
        </div>
    </div>
    
    <div class="flex-container">
        <div class="flex-item">
            <h3>Controller Code (AdminController.php)</h3>
            <pre>if ($request->hasFile(\'profile\')) {
    $profilePhoto = $request->file(\'profile\');
    
    // Read file content as binary data
    $imageData = file_get_contents($profilePhoto->getPathname());
    
    // Store binary data in database
    $updateData[\'profile\'] = $imageData;
}</pre>
        </div>
        
        <div class="flex-item">
            <h3>JavaScript Preview</h3>
            <pre>function previewProfilePicture(event) {
    const input = event.target;
    const reader = new FileReader();
    
    reader.onload = function() {
        document.getElementById(\'profilePreview\').src = reader.result;
    };
    
    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    }
}</pre>
        </div>
    </div>
</div>';

echo '<div class="card">
    <h2>6. Troubleshooting</h2>
    
    <h3>Common Issues</h3>
    <ul>
        <li><strong>Profile images not displaying:</strong> Make sure the API URL is correct and the user has a profile image in the database.</li>
        <li><strong>Upload not working:</strong> Ensure the form has <code>enctype="multipart/form-data"</code> and the controller is storing the image correctly.</li>
        <li><strong>Database errors:</strong> Check that the profile column is defined as LONGBLOB to store binary data.</li>
        <li><strong>Caching issues:</strong> Add a timestamp parameter to force the browser to reload the image: <code>&t=' . time() . '</code></li>
    </ul>
    
    <h3>Debug Tools</h3>
    <ul>
        <li><a href="/check_profile_images.php" target="_blank">check_profile_images.php</a> - Check for users with profile images</li>
        <li><a href="/api/profile_image.php?id=1&debug=1" target="_blank">profile_image.php?id=1&debug=1</a> - Test the API with debug mode</li>
        <li><a href="/test_profile_image.php" target="_blank">test_profile_image.php</a> - View profile images for all users</li>
    </ul>
</div>';

echo '</body></html>';
