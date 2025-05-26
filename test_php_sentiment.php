<?php

require __DIR__.'/vendor/autoload.php';

use App\Services\SentimentAnalysisService;

// Test the sentiment analysis with the example
$testText = "It is late at night pero ung mga kapitbahay ang lakas ng videoke hindi kami makatulog Nakakainis na at paulit-ulit";

echo "Testing Sentiment Analysis\n";
echo "==================================================\n";
echo "Original text: " . $testText . "\n\n";

$sentimentService = new SentimentAnalysisService();
$sentiment = $sentimentService->analyzeSentiment($testText);

echo "Result: " . strtoupper($sentiment) . "\n\n";

// Display the badge
echo "Badge HTML: " . $sentimentService->getSentimentBadge($sentiment) . "\n";

// Testing different texts
$testCases = [
    "I'm very happy with the service provided by the barangay staff.",
    "The garbage collection is not working properly and there's trash everywhere.",
    "The road construction is ongoing but it will be beneficial once completed.",
    "ang ingay ng kapitbahay hindi kami makatulog sa gabi",
    "salamat po sa mabilis na pag-ayos ng aming reklamo"
];

echo "\nTesting Multiple Sentences:\n";
echo "==================================================\n";

foreach ($testCases as $text) {
    $sentiment = $sentimentService->analyzeSentiment($text);
    echo "Text: \"$text\"\n";
    echo "Sentiment: " . strtoupper($sentiment) . "\n";
    echo "--------------------------------------------------\n";
}

echo "\nDone!\n";
