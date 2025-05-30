<?php
// Debug script to compare different avatar methods
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get database credentials from Laravel's configuration
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Helper function to output image tag with error handling
function outputImage($src, $title) {
    echo "<div style='margin: 10px; border: 1px solid #ccc; padding: 10px; display: inline-block; text-align: center;'>";
    echo "<h3>$title</h3>";
    echo "<img src='$src' width='100' height='100' style='object-fit: cover;' onerror=\"this.onerror=null; this.src='https://ui-avatars.com/api/?name=Error&color=dc3545&background=f8d7da&size=100'; this.style.border='2px solid red';\">";
    echo "<p>URL: <code>$src</code></p>";
    echo "</div>";
}

// Get authenticated user or fallback to user by ID
$user = null;
$userId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (Auth::check()) {
    $user = Auth::user();
} else if ($userId) {
    $user = App\Models\User::find($userId);
} else {
    // Try to get the first admin user
    $user = App\Models\User::where('role', 'admin')->orWhere('role', 'super_admin')->first();
}

if (!$user) {
    echo "<h1>No user found</h1>";
    echo "<p>Please login or specify a user ID in the query string (?id=X)</p>";
    exit;
}

echo "<html><head><title>Avatar Debug</title>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; }</style>";
echo "</head><body>";

echo "<h1>Avatar Debug for User: {$user->name} (ID: {$user->id})</h1>";
echo "<p>Email: {$user->email}</p>";
echo "<p>Has profile image: " . ($user->profile ? "Yes (".strlen($user->profile)." bytes)" : "No") . "</p>";

echo "<h2>All Avatar Methods</h2>";

// Test all avatar methods
outputImage($user->getAvatarUrl(100, 'ui-avatars', true), "getAvatarUrl() with debug");
outputImage($user->getAvatarUrl(100, 'ui-avatars'), "getAvatarUrl() default");
outputImage($user->getUIAvatarsUrl(100), "getUIAvatarsUrl()");
outputImage($user->getGravatarUrl(100), "getGravatarUrl()");

// Test direct methods
outputImage(url("/api/profile_image.php?id={$user->id}&t=".time()), "Direct API URL");
outputImage(url("/direct_image_test.php?id={$user->id}"), "direct_image_test.php");

// Show HTML code example
echo "<h2>HTML Code Examples</h2>";
echo "<pre>";
echo htmlspecialchars("<img src=\"{{ Auth::user()->getAvatarUrl(30) }}\" alt=\"Avatar\">");
echo "</pre>";

echo "</body></html>";
