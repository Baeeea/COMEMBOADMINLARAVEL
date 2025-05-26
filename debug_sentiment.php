<?php

require __DIR__.'/vendor/autoload.php';

use App\Services\SentimentAnalysisService;

function testSentiment($text) {
    echo "Testing sentiment for: $text\n";

    $service = new SentimentAnalysisService();

    try {
        // Direct test of Python service
        $command = $service->pythonExecutable . ' "' . $service->pythonScriptPath . '" ' . escapeshellarg($text) . ' 2>&1';
        echo "Running command: $command\n";
        $output = shell_exec($command);
        echo "Python output: $output\n";

        // Test the PHP service
        $sentiment = $service->analyzeSentiment($text);
        echo "Final sentiment: $sentiment\n";
        echo "----------------------------------------\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Test cases
$tests = [
    "Loud karaoke until midnight",
    "The neighbors are playing loud music at night",
    "Thank you for your help",
    "Garbage not collected",
];

foreach ($tests as $test) {
    testSentiment($test);
}
