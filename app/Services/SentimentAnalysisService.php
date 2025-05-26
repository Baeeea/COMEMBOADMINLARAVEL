<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SentimentAnalysisService
{
    private $pythonScriptPath;
    private $pythonExecutable;

    public function __construct()
    {
        try {
            if (function_exists('app_path')) {
                $this->pythonScriptPath = app_path('Services/SentimentAnalysisService.py');
            } else {
                throw new \Exception('app_path not available');
            }
        } catch (\Throwable $e) {
            $this->pythonScriptPath = __DIR__ . DIRECTORY_SEPARATOR . 'SentimentAnalysisService.py';
        }
        $this->pythonExecutable = $this->findPythonExecutable();
    }

    /**
     * Find Python executable on the system
     */
    private function findPythonExecutable()
    {
        $possiblePaths = ['python3', 'python', 'py'];

        foreach ($possiblePaths as $path) {
            $output = shell_exec("$path --version 2>&1");
            if ($output && strpos($output, 'Python') !== false) {
                Log::debug("Found Python executable", ['path' => $path, 'version' => $output]);
                return $path;
            }
        }

        Log::warning("No Python executable found, using default");
        return 'python3';
    }

    /**
     * Analyze sentiment of given text
     */
    public function analyzeSentiment($text)
    {
        try {
            $text = trim($text);
            if (empty($text)) {
                Log::info("Empty text provided for sentiment analysis");
                return [
                    'sentiment' => 'neutral',
                    'success' => true,
                    'scores' => ['negative' => 0, 'positive' => 0, 'neutral' => 100],
                    'tokens' => [],
                    'matched_tokens' => ['negative' => [], 'positive' => [], 'neutral' => []]
                ];
            }

            // First try the simple PHP analysis
            $phpSentiment = $this->simpleSentimentAnalysis($text);
            Log::debug("PHP sentiment analysis result", [
                'text' => $text,
                'sentiment' => $phpSentiment['sentiment'] ?? 'neutral'
            ]);

            // Validate Python environment
            if (!file_exists($this->pythonScriptPath)) {
                Log::error("Python script not found", ['path' => $this->pythonScriptPath]);
                return $phpSentiment;
            }

            if (!is_executable($this->pythonExecutable)) {
                Log::error("Python executable not found or not executable", [
                    'path' => $this->pythonExecutable
                ]);
                return $phpSentiment;
            }

            // Prepare Python command with proper escaping
            $escapedText = escapeshellarg($text);
            $command = sprintf('%s -B "%s" %s 2>&1',
                $this->pythonExecutable,
                $this->pythonScriptPath,
                $escapedText
            );

            Log::debug("Executing sentiment analysis command", [
                'command' => $command,
                'text' => $text
            ]);

            $output = shell_exec($command);

            if ($output === null || $output === false) {
                Log::error("Failed to execute Python command", [
                    'command' => $command,
                    'error' => error_get_last()
                ]);
                return $phpSentiment;
            }

            Log::debug("Raw Python output", ['output' => $output]);

            // Process output - look for JSON in multiple lines
            if (!$output) {
                Log::error("No output from Python script");
                return $phpSentiment;
            }

            // Split by newlines and look for JSON data
            $lines = array_filter(explode("\n", trim($output)));
            $jsonLine = null;

            foreach ($lines as $line) {
                // Skip debug/error lines
                if (strpos($line, '[DEBUG]') === 0 || strpos($line, '[ERROR]') === 0) {
                    continue;
                }

                // Try to decode the line as JSON
                $decoded = json_decode($line, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['sentiment'])) {
                    $jsonLine = $line;
                    break;
                }
            }

            if (!$jsonLine) {
                Log::error("No valid JSON output found in Python response", [
                    'output' => $output,
                    'lines' => $lines
                ]);
                return $phpSentiment;
            }

            $result = json_decode($jsonLine, true);
            Log::debug("Decoded Python result", ['result' => $result]);

            if (!isset($result['sentiment'])) {
                Log::error("Invalid result format from Python script", [
                    'result' => $result,
                    'json_line' => $jsonLine
                ]);
                return $phpSentiment;
            }

            // For high-impact complaints (those with weighted words), force negative sentiment
            if (isset($result['matched_tokens']['negative']) && 
                !empty($result['matched_tokens']['negative']) && 
                strpos(json_encode($result['matched_tokens']['negative']), 'Weighted:') !== false) {
                $result['sentiment'] = 'negative';
            }

            // Ensure we return a properly formatted array
            return [
                'sentiment' => strtolower($result['sentiment']),
                'success' => true,
                'scores' => isset($result['scores']) ? array_map('floatval', $result['scores']) : ['negative' => 0, 'positive' => 0, 'neutral' => 100],
                'tokens' => $result['tokens'] ?? [],
                'matched_tokens' => $result['matched_tokens'] ?? ['negative' => [], 'positive' => [], 'neutral' => []]
            ];

        } catch (\Exception $e) {
            Log::error('Sentiment analysis error', [
                'text' => $text,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'python_path' => $this->pythonExecutable,
                'script_path' => $this->pythonScriptPath
            ]);
            return $phpSentiment;
        }
    }

    /**
     * Simple fallback sentiment analysis
     */
    private function simpleSentimentAnalysis($text)
    {
        $text = strtolower($text);
        // Tokenize text
        $tokens = preg_split('/\s+/', preg_replace('/[^\w\s]/u', ' ', $text));

        // Log tokenization
        Log::debug("PHP fallback tokenization", [
            'text' => $text,
            'tokens' => $tokens
        ]);

        if (empty($tokens)) {
            return [
                'sentiment' => 'neutral',
                'success' => true,
                'scores' => ['negative' => 0, 'positive' => 0, 'neutral' => 100],
                'tokens' => [],
                'matched_tokens' => ['negative' => [], 'positive' => [], 'neutral' => []]
            ];
        }

        // Lexicon and analysis logic as before but track matches
        $positiveMatched = [];
        $negativeMatched = [];
        $neutralMatched = [];

        // Sentiment lexicon (should match Python logic)
        $positiveKeywords = [
            'good', 'great', 'excellent', 'amazing', 'wonderful', 'fantastic',
            'satisfied', 'happy', 'pleased', 'thank', 'appreciate', 'nice',
            'beautiful', 'clean', 'quiet', 'peaceful',
            'maganda', 'salamat', 'masaya', 'satisfied', 'ok', 'ayos',
            'tahimik', 'malinis', 'ganda', 'okay'
        ];
        $negativeKeywords = [
            'bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'angry',
            'frustrated', 'disappointed', 'complaint', 'problem', 'issue',
            'annoying', 'disturbing', 'loud', 'noisy', 'late', 'night', 'midnight',
            'karaoke', 'disturbance', 'disturb', 'complain', 'until',
            'pangit', 'galit', 'hindi', 'masama', 'problema', 'reklamo',
            'ingay', 'maingay', 'basura', 'dumi', 'amoy', 'nakakainis',
            'paulit', 'ulit', 'kapitbahay', 'lakas', 'videoke', 'karaoke', 'makatulog',
            'nakakabadtrip', 'badtrip', 'stress', 'nakakastress', 'madaling-araw',
            // Infrastructure and environmental issues (matching Python lexicon)
            'sewage', 'leaking', 'leak', 'broken', 'damaged', 'dirty', 'filthy',
            'smelly', 'stinking', 'polluted', 'contaminated', 'clogged', 'blocked',
            'overflow', 'flooding', 'flooded', 'waste', 'garbage', 'trash',
            'pothole', 'cracked', 'unsafe', 'dangerous', 'hazardous', 'broken',
            'malfunctioning', 'defective', 'deteriorating', 'rotting', 'rusty',
            'corroded', 'collapsed', 'faulty', 'poor', 'inadequate', 'insufficient',
            'lacking', 'missing', 'absent', 'unavailable', 'inaccessible',
            // Water and sanitation issues
            'sewerage', 'drainage', 'plumbing', 'pipe', 'pipes', 'sewer',
            'septic', 'backup', 'clog', 'blockage', 'burst',
            'rupture', 'crack', 'hole', 'opening', 'breach', 'damage',
            // Road and infrastructure
            'pothole', 'crater', 'bump', 'uneven', 'rough', 'damaged',
            'deteriorated', 'worn', 'eroded', 'crumbling', 'breaking',
            // Safety and health concerns
            'unsanitary', 'unhygienic', 'health', 'risk', 'hazard', 'danger',
            'threat', 'unsafe', 'contamination', 'infection', 'disease',
            'illness', 'sick', 'poison', 'toxic', 'harmful', 'unhealthy'
        ];
        $neutralKeywords = [
            'time', 'day', 'night', 'morning', 'afternoon', 'evening',
            'house', 'home', 'place', 'area', 'street', 'neighbor',
            'water', 'road', 'sidewalk', 'pavement', 'construction',
            'repair', 'maintenance', 'work', 'project', 'installation',
            'location', 'address', 'building', 'structure', 'facility',
            'infrastructure', 'system', 'service', 'utility', 'public',
            'government', 'municipal', 'city', 'barangay', 'community'
        ];

        $positiveCount = 0;
        $negativeCount = 0;
        $neutralCount = 0;

        foreach ($tokens as $token) {
            if (in_array($token, $negativeKeywords)) {
                $negativeCount++;
                $negativeMatched[] = $token;
            } elseif (in_array($token, $positiveKeywords)) {
                $positiveCount++;
                $positiveMatched[] = $token;
            } elseif (in_array($token, $neutralKeywords)) {
                $neutralCount++;
                $neutralMatched[] = $token;
            }
        }

        $total = $positiveCount + $negativeCount + $neutralCount;

        if ($total === 0) {
            return [
                'sentiment' => 'neutral',
                'success' => true,
                'scores' => ['negative' => 0, 'positive' => 0, 'neutral' => 100],
                'tokens' => $tokens,
                'matched_tokens' => ['negative' => [], 'positive' => [], 'neutral' => []]
            ];
        }

        // Calculate scores
        $scores = [
            'negative' => round(($negativeCount / $total) * 100, 2),
            'positive' => round(($positiveCount / $total) * 100, 2),
            'neutral' => round(($neutralCount / $total) * 100, 2)
        ];

        // Determine sentiment using improved logic (matching Python version)
        $sentiment = 'neutral';
        if ($negativeCount > 0 && (($negativeCount / $total) >= 0.25 || $negativeCount >= $positiveCount)) {
            $sentiment = 'negative';
        } elseif ($positiveCount > 0 && $positiveCount > $negativeCount && ($positiveCount / $total) >= 0.25) {
            $sentiment = 'positive';
        }

        return [
            'sentiment' => $sentiment,
            'success' => true,
            'scores' => $scores,
            'tokens' => $tokens,
            'matched_tokens' => [
                'negative' => $negativeMatched,
                'positive' => $positiveMatched,
                'neutral' => $neutralMatched
            ]
        ];
    }

    /**
     * Get sentiment label with styling
     */
    public function getSentimentBadge($sentiment)
    {
        switch ($sentiment) {
            case 'positive':
                return '<span class="badge bg-success">Positive</span>';
            case 'negative':
                return '<span class="badge bg-danger">Negative</span>';
            case 'neutral':
            default:
                return '<span class="badge bg-secondary">Neutral</span>';
        }
    }

    /**
     * Analyze sentiment for all complaints in database
     */
    public function batchAnalyzeComplaints()
    {
        try {
            $complaints = DB::table('complaintrequests')
                ->whereNotNull('specific_description')
                ->where('specific_description', '!=', '')
                ->get();

            $results = [];

            foreach ($complaints as $complaint) {
                $sentiment = $this->analyzeSentiment($complaint->specific_description);

                $results[] = [
                    'user_id' => $complaint->user_id,
                    'sentiment' => $sentiment,
                    'description' => substr($complaint->specific_description, 0, 100) .
                                   (strlen($complaint->specific_description) > 100 ? '...' : '')
                ];
            }

            return $results;

        } catch (\Exception $e) {
            Log::error('Batch sentiment analysis error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Install required Python packages
     */
    public function installRequirements()
    {
        try {
            $packages = ['textblob', 'scikit-learn', 'pandas', 'mysql-connector-python'];

            foreach ($packages as $package) {
                $command = "{$this->pythonExecutable} -m pip install {$package}";
                $output = shell_exec($command . ' 2>&1');
                Log::info("Installing {$package}: " . $output);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error installing Python packages: ' . $e->getMessage());
            return false;
        }
    }
}
