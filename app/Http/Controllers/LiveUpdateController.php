<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LiveUpdateController extends Controller
{
    public function stream()
    {
        $response = response()->stream(function () {
            $clientId = uniqid('client_', true);
            Log::info("Live update stream started for client: {$clientId}");
            
            // Set initial client connection time
            Cache::put("client_{$clientId}_connected", time(), 3600);
            
            while (true) {
                try {
                    $currentTime = time();
                    
                    // Send keep-alive every 15 seconds (faster than before)
                    if ($currentTime % 15 == 0) {
                        echo "event: keepalive\n";
                        echo "data: " . json_encode([
                            'timestamp' => $currentTime,
                            'client_id' => $clientId
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                    }
                    
                    // Check for database updates every second for faster response
                    $lastUpdate = Cache::get('last_database_update', 0);
                    $clientLastUpdate = Cache::get("client_{$clientId}_last_update", 0);
                    
                    if ($lastUpdate > $clientLastUpdate) {
                        $updateDetails = Cache::get('database_update_details', []);
                        
                        echo "event: database_update\n";
                        echo "data: " . json_encode([
                            'timestamp' => $lastUpdate,
                            'message' => 'Database updated - refreshing...',
                            'details' => $updateDetails,
                            'client_id' => $clientId
                        ]) . "\n\n";
                        
                        Cache::put("client_{$clientId}_last_update", $lastUpdate, 3600);
                        ob_flush();
                        flush();
                    }
                    
                    // Check for specific table updates
                    $this->checkTableUpdates($clientId);
                    
                    sleep(1); // Check every 1 second for faster response
                    
                } catch (\Exception $e) {
                    Log::error("Live update stream error: " . $e->getMessage());
                    echo "event: error\n";
                    echo "data: " . json_encode(['message' => 'Stream error occurred']) . "\n\n";
                    ob_flush();
                    flush();
                    break;
                }
            }
            
            // Clean up client data
            Cache::forget("client_{$clientId}_connected");
            Cache::forget("client_{$clientId}_last_update");
            Log::info("Live update stream ended for client: {$clientId}");
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', 'Cache-Control');
        
        return $response;
    }
    
    private function checkTableUpdates($clientId)
    {
        try {
            // Check for specific table changes that matter for real-time updates
            $importantTables = ['residents', 'announcements', 'news', 'feedbacks', 'complaint_requests'];
            
            foreach ($importantTables as $table) {
                $lastTableUpdate = Cache::get("table_{$table}_last_update", 0);
                $clientLastTableUpdate = Cache::get("client_{$clientId}_table_{$table}_update", 0);
                
                if ($lastTableUpdate > $clientLastTableUpdate) {
                    echo "event: table_update\n";
                    echo "data: " . json_encode([
                        'table' => $table,
                        'timestamp' => $lastTableUpdate,
                        'message' => ucfirst($table) . ' data updated',
                        'client_id' => $clientId
                    ]) . "\n\n";
                    
                    Cache::put("client_{$clientId}_table_{$table}_update", $lastTableUpdate, 3600);
                    ob_flush();
                    flush();
                }
            }
        } catch (\Exception $e) {
            Log::error("Table update check error: " . $e->getMessage());
        }
    }
    
    public function triggerUpdate(Request $request)
    {
        $timestamp = time();
        $table = $request->get('table', 'general');
        $action = $request->get('action', 'update');
        $details = $request->get('details', []);
        
        // Set general database update
        Cache::put('last_database_update', $timestamp, 3600);
        
        // Set specific table update if provided
        if ($table !== 'general') {
            Cache::put("table_{$table}_last_update", $timestamp, 3600);
        }
        
        // Store update details
        Cache::put('database_update_details', [
            'table' => $table,
            'action' => $action,
            'timestamp' => $timestamp,
            'details' => $details
        ], 3600);
        
        Log::info("Database update triggered", [
            'table' => $table,
            'action' => $action,
            'timestamp' => $timestamp
        ]);
        
        return response()->json([
            'status' => 'success', 
            'message' => 'Update triggered',
            'timestamp' => $timestamp,
            'table' => $table,
            'action' => $action
        ]);
    }
    
    public function getStatus()
    {
        $connectedClients = [];
        $cacheKeys = Cache::getRedis()->keys('*client_*_connected*');
        
        foreach ($cacheKeys as $key) {
            $clientId = preg_replace('/.*client_([^_]+)_connected.*/', '$1', $key);
            $connectedTime = Cache::get($key);
            if ($connectedTime) {
                $connectedClients[] = [
                    'client_id' => $clientId,
                    'connected_since' => $connectedTime,
                    'duration' => time() - $connectedTime
                ];
            }
        }
        
        return response()->json([
            'status' => 'active',
            'connected_clients' => count($connectedClients),
            'clients' => $connectedClients,
            'last_update' => Cache::get('last_database_update', 0),
            'system_time' => time()
        ]);
    }
}
