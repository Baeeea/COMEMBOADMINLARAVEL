<?php
echo "Creating test news data...\n";

// Simple approach without Laravel
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    
    // Create news table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS news (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        Title VARCHAR(255),
        content TEXT,
        createdAt DATETIME,
        image VARCHAR(255),
        created_at DATETIME,
        updated_at DATETIME
    )");
    
    // Clear existing data
    $pdo->exec("DELETE FROM news");
    
    // Insert test data
    $stmt = $pdo->prepare("INSERT INTO news (Title, content, createdAt, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
    
    $newsData = [
        ['Community Clean-up Drive', 'Join us for a community-wide clean-up drive this Saturday.', '2025-05-20', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
        ['Basketball Tournament Registration', 'Registration for the annual basketball tournament is now open.', '2025-05-21', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')],
        ['Health Check-up Schedule', 'Free health check-ups will be available at the barangay health center.', '2025-05-22', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]
    ];
    
    foreach ($newsData as $data) {
        $stmt->execute($data);
    }
    
    echo "Successfully inserted " . count($newsData) . " news records.\n";
    
    // Verify records
    $result = $pdo->query("SELECT id, Title FROM news");
    echo "\nNews records:\n";
    while ($row = $result->fetch()) {
        echo "ID: {$row['id']}, Title: {$row['Title']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
