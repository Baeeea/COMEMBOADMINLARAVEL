<?php
// Simple test page to debug ID upload functionality
echo "<h1>ID Upload Debug Test</h1>";

// Check if storage directory exists
$storageDir = __DIR__ . '/../storage/app/public/uploads/ids';
echo "<h2>Storage Directory Check:</h2>";
echo "<p>Directory: " . $storageDir . "</p>";
echo "<p>Exists: " . (is_dir($storageDir) ? 'YES' : 'NO') . "</p>";
echo "<p>Writable: " . (is_writable($storageDir) ? 'YES' : 'NO') . "</p>";

// Check if public/storage symlink exists
$symlinkDir = __DIR__ . '/storage';
echo "<h2>Public Storage Symlink Check:</h2>";
echo "<p>Symlink: " . $symlinkDir . "</p>";
echo "<p>Exists: " . (is_link($symlinkDir) ? 'YES' : 'NO') . "</p>";
echo "<p>Valid: " . (is_dir($symlinkDir) ? 'YES' : 'NO') . "</p>";

// Test form
echo "<h2>Test Upload Form:</h2>";
echo '<form action="test_id_upload_handler.php" method="POST" enctype="multipart/form-data">';
echo '<label>Test ID Front:</label><br>';
echo '<input type="file" name="validIDFront" accept="image/*"><br><br>';
echo '<label>Test ID Back:</label><br>';
echo '<input type="file" name="validIDBack" accept="image/*"><br><br>';
echo '<input type="submit" value="Test Upload">';
echo '</form>';
?>
