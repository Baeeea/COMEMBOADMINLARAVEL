<?php
// Test script for profile image API
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$userId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$userId) {
    // If no ID provided, find users with profile images
    $users = \App\Models\Admin::whereNotNull('profile')->get();
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Profile Image Test</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .user-card { border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 5px; }
            .profile-image { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
        </style>
    </head>
    <body>
        <h1>Profile Image Test</h1>";
        
    if ($users->count() > 0) {
        foreach ($users as $user) {
            echo "<div class='user-card'>";
            echo "<h3>" . htmlspecialchars($user->name) . " (ID: {$user->id})</h3>";
            echo "<p>Email: " . htmlspecialchars($user->email) . "</p>";
            echo "<div>Profile Image:</div>";
            echo "<img class='profile-image' src='/api/profile_image.php?id={$user->id}&t=" . time() . "' alt='Profile Image'>";
            echo "<p>Raw API URL: <code>/api/profile_image.php?id={$user->id}</code></p>";
            echo "</div>";
        }
    } else {
        echo "<p>No users found with profile images.</p>";
        echo "<p>To upload a profile image, go to your profile page and click 'Edit Account'.</p>";
    }
    
    echo "</body></html>";
} else {
    // Show details for a specific user
    $user = \App\Models\Admin::find($userId);
    
    if (!$user) {
        echo "User not found.";
        exit;
    }
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Profile Image for " . htmlspecialchars($user->name) . "</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .profile-container { display: flex; gap: 20px; align-items: flex-start; }
            .profile-image { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; }
            .profile-details { flex: 1; }
            .api-details { margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 5px; }
            code { background: #eee; padding: 2px 4px; border-radius: 3px; }
        </style>
    </head>
    <body>
        <h1>Profile Image Test for " . htmlspecialchars($user->name) . "</h1>";
        
    echo "<div class='profile-container'>";
    echo "<img class='profile-image' src='/api/profile_image.php?id={$user->id}&t=" . time() . "' alt='Profile Image'>";
    
    echo "<div class='profile-details'>
            <h3>User Details</h3>
            <p><strong>ID:</strong> {$user->id}</p>
            <p><strong>Name:</strong> " . htmlspecialchars($user->name) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($user->email) . "</p>";
            
    if ($user->profile) {
        $profileSize = strlen($user->profile);
        echo "<p><strong>Profile Image Size:</strong> " . round($profileSize / 1024, 2) . " KB</p>";
        
        // Try to detect the MIME type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($user->profile);
        echo "<p><strong>MIME Type:</strong> {$mimeType}</p>";
    } else {
        echo "<p>No profile image found for this user.</p>";
    }
    
    echo "</div></div>";
    
    echo "<div class='api-details'>
            <h3>API Information</h3>
            <p><strong>Direct API URL:</strong> <code>/api/profile_image.php?id={$user->id}</code></p>
            <p>Use this URL in image tags to display the user's profile image:</p>
            <pre><code>&lt;img src=\"/api/profile_image.php?id={$user->id}&t=[timestamp]\" alt=\"Profile Image\"&gt;</code></pre>
            <p>Add a timestamp parameter (<code>&t=" . time() . "</code>) to prevent caching issues when updating the image.</p>
          </div>";
    
    echo "<p><a href='test_profile_image.php'>← Back to all users</a></p>";
    echo "</body></html>";
}
