<?php
echo "Testing MySQL connection and creating test data...\n";

try {
    // Test MySQL connection
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=barangaycomembo', 'root', '');
    echo "✓ MySQL connection successful!\n";
    
    // Check if news table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'news'");
    if ($stmt->rowCount() == 0) {
        echo "News table doesn't exist. Creating it...\n";
        
        // Create news table
        $createTable = "CREATE TABLE news (
            id INT AUTO_INCREMENT PRIMARY KEY,
            Title VARCHAR(255) NOT NULL,
            content TEXT,
            createdAt DATETIME,
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($createTable);
        echo "✓ News table created!\n";
    } else {
        echo "✓ News table exists!\n";
    }
    
    // Clear existing data and insert test records
    $pdo->exec("DELETE FROM news");
    echo "Cleared existing news records.\n";
    
    // Insert test data with proper auto-increment IDs
    $stmt = $pdo->prepare("INSERT INTO news (Title, content, createdAt, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    
    $newsData = [
        ['Community Clean-up Drive', 'Join us for a community-wide clean-up drive this Saturday. Help make our barangay cleaner and greener!', '2025-05-20'],
        ['Basketball Tournament Registration', 'Registration for the annual basketball tournament is now open. Sign up at the barangay hall.', '2025-05-21'],
        ['Health Check-up Schedule', 'Free health check-ups will be available at the barangay health center every Wednesday.', '2025-05-22']
    ];
    
    foreach ($newsData as $data) {
        $stmt->execute($data);
    }
    
    echo "✓ Inserted " . count($newsData) . " test news records!\n";
    
    // Verify the records have proper IDs
    $result = $pdo->query("SELECT id, Title FROM news ORDER BY id");
    echo "\nNews records in MySQL database:\n";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Title: {$row['Title']}\n";
    }
    
    echo "\n✓ MySQL database is ready! You can now test the news functionality.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting tips:\n";
    echo "1. Make sure MySQL is running (check if you can connect via MySQL Workbench or phpMyAdmin)\n";
    echo "2. Verify the database 'barangaycomembo' exists\n";
    echo "3. Check if the MySQL credentials are correct\n";
}
