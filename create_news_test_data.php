<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Creating news records for testing...\n";

try {
    // Create some test news records
    $newsData = [
        [
            'Title' => 'Community Clean-up Drive',
            'content' => 'Join us for a community-wide clean-up drive this Saturday. Help make our barangay cleaner and greener!',
            'createdAt' => '2025-05-20 10:00:00',
            'image' => null,
        ],
        [
            'Title' => 'Basketball Tournament Registration',
            'content' => 'Registration for the annual basketball tournament is now open. Sign up at the barangay hall.',
            'createdAt' => '2025-05-21 14:30:00',
            'image' => null,
        ],
        [
            'Title' => 'Health Check-up Schedule',
            'content' => 'Free health check-ups will be available at the barangay health center every Wednesday.',
            'createdAt' => '2025-05-22 09:15:00',
            'image' => null,
        ]
    ];
    
    foreach ($newsData as $data) {
        $news = App\Models\News::create($data);
        echo "Created news: ID {$news->id} - {$news->Title}\n";
    }
    
    echo "\nTest data created successfully!\n";
    echo "You can now test the edit functionality by clicking Edit on any news item.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // If Laravel fails, try direct database approach
    echo "Trying direct database approach...\n";
    
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=barangaycomembo', 'root', '');
        
        $stmt = $pdo->prepare("INSERT INTO news (Title, content, createdAt, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        
        foreach ($newsData as $data) {
            $stmt->execute([$data['Title'], $data['content'], $data['createdAt']]);
            echo "Inserted: {$data['Title']}\n";
        }
        
        echo "Direct database insertion successful!\n";
        
    } catch (Exception $e2) {
        echo "Database error: " . $e2->getMessage() . "\n";
    }
}
