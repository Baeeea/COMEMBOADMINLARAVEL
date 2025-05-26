<?php
// Add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Services/SentimentAnalysisService.php';

use App\Services\SentimentAnalysisService;

echo "Testing web sentiment analysis...\n";

try {
    $service = new SentimentAnalysisService();

    $testTexts = [
        "Sewage water leaking into road",
        "Broken pipe flooding the street", 
        "Thank you for the excellent service",
        "The road needs repair"
    ];

    foreach ($testTexts as $text) {
        echo "\n--- Testing: '$text' ---\n";
        $result = $service->analyzeSentiment($text);
        echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
