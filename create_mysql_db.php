<?php

echo "Creating MySQL database...\n";

try {
    // Connect to MySQL server without specifying database
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS barangaycomembo");
    echo "Database 'barangaycomembo' created or already exists.\n";
    
    // Switch to the database
    $pdo->exec("USE barangaycomembo");
    
    // Check if news table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'news'");
    if ($stmt->rowCount() > 0) {
        echo "News table already exists.\n";
        
        // Check records
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM news");
        $count = $stmt->fetch()['count'];
        echo "News table has {$count} records.\n";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, Title FROM news LIMIT 5");
            echo "Sample records:\n";
            while ($row = $stmt->fetch()) {
                echo "- ID: {$row['id']}, Title: {$row['Title']}\n";
            }
        }
    } else {
        echo "News table does not exist. Need to run migrations.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // Try with different port (Herd might use different port)
    echo "Trying port 33060...\n";
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;port=33060', 'root', '');
        echo "Connected on port 33060!\n";
    } catch (Exception $e2) {
        echo "Port 33060 failed: " . $e2->getMessage() . "\n";
    }
}
