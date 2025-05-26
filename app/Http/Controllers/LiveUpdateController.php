<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LiveUpdateController extends Controller
{
    public function stream()
    {
        $response = response()->stream(function () {
            while (true) {
                // Check for updates in cache
                $lastUpdate = Cache::get('last_database_update', 0);
                $currentTime = time();
                
                // Send keep-alive every 30 seconds
                if ($currentTime % 30 == 0) {
                    echo "event: keepalive\n";
                    echo "data: " . json_encode(['timestamp' => $currentTime]) . "\n\n";
                    ob_flush();
                    flush();
                }
                
                // Check if there's been a database update
                $clientLastUpdate = Cache::get('client_last_update', 0);
                if ($lastUpdate > $clientLastUpdate) {
                    echo "event: database_update\n";
                    echo "data: " . json_encode([
                        'timestamp' => $lastUpdate,
                        'message' => 'Database updated - reloading...'
                    ]) . "\n\n";
                    
                    Cache::put('client_last_update', $lastUpdate, 3600);
                    ob_flush();
                    flush();
                }
                
                sleep(2); // Check every 2 seconds
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no'); // Disable nginx buffering
        
        return $response;
    }
    
    public function triggerUpdate()
    {
        Cache::put('last_database_update', time(), 3600);
        return response()->json(['status' => 'success', 'message' => 'Update triggered']);
    }
}
