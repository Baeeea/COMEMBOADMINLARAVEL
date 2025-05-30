<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class DatabaseMonitorController extends Controller
{
    /**
     * The storage path for snapshots
     * 
     * @var string
     */
    protected $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/db-snapshots');
        
        if (!File::exists($this->storagePath)) {
            File::makeDirectory($this->storagePath, 0755, true);
        }
    }

    /**
     * Display the database monitor dashboard
     */
    public function index()
    {
        // Get database info
        $dbInfo = [
            'name' => DB::connection()->getDatabaseName(),
            'driver' => DB::connection()->getDriverName(),
            'version' => $this->getDatabaseVersion(),
            'tables' => count(DB::select('SHOW TABLES')),
        ];
        
        // Get snapshots list
        $snapshots = $this->getSnapshots();
        
        // Get changelog
        $changelog = $this->getChangelog();
        
        // Get messages table status
        $messagesStatus = $this->getMessagesTableStatus();
        
        return view('database.monitor', compact('dbInfo', 'snapshots', 'changelog', 'messagesStatus'));
    }
    
    /**
     * Take a new snapshot
     */
    public function takeSnapshot()
    {
        try {
            $exitCode = Artisan::call('db:track-changes');
            $output = Artisan::output();
            
            return redirect()->route('db.monitor')->with('success', 'Database snapshot taken successfully!');
        } catch (\Exception $e) {
            return redirect()->route('db.monitor')->with('error', 'Failed to take snapshot: ' . $e->getMessage());
        }
    }
    
    /**
     * Compare snapshots
     */
    public function compareSnapshots(Request $request)
    {
        $request->validate([
            'snapshot_id' => 'required|string',
        ]);
        
        try {
            $exitCode = Artisan::call('db:track-changes', [
                '--compare' => $request->snapshot_id
            ]);
            $output = Artisan::output();
            
            return redirect()->route('db.monitor')->with('info', 'Comparison completed. Check the changelog for details.');
        } catch (\Exception $e) {
            return redirect()->route('db.monitor')->with('error', 'Failed to compare snapshots: ' . $e->getMessage());
        }
    }
    
    /**
     * Fix messages table issues
     */
    public function fixMessagesTable()
    {
        try {
            $exitCode = Artisan::call('db:monitor-messages', [
                '--fix' => true
            ]);
            $output = Artisan::output();
            
            return redirect()->route('db.monitor')->with('success', 'Messages table fixes attempted. ' . strip_tags($output));
        } catch (\Exception $e) {
            return redirect()->route('db.monitor')->with('error', 'Failed to fix messages table: ' . $e->getMessage());
        }
    }
    
    /**
     * View snapshot details
     */
    public function viewSnapshot($id)
    {
        $snapshotPath = $this->storagePath . "/{$id}.json";
        
        if (!File::exists($snapshotPath)) {
            return redirect()->route('db.monitor')->with('error', 'Snapshot not found');
        }
        
        $snapshot = json_decode(File::get($snapshotPath), true);
        
        return view('database.snapshot', compact('snapshot'));
    }
    
    /**
     * Delete a snapshot
     */
    public function deleteSnapshot($id)
    {
        $snapshotPath = $this->storagePath . "/{$id}.json";
        
        if (!File::exists($snapshotPath)) {
            return redirect()->route('db.monitor')->with('error', 'Snapshot not found');
        }
        
        File::delete($snapshotPath);
        
        return redirect()->route('db.monitor')->with('success', 'Snapshot deleted successfully');
    }
    
    /**
     * Get database version
     */
    protected function getDatabaseVersion()
    {
        try {
            $result = DB::select('SELECT VERSION() as version');
            return $result[0]->version ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Could not determine version';
        }
    }
    
    /**
     * Get snapshots list
     */
    protected function getSnapshots()
    {
        $files = File::files($this->storagePath);
        $snapshots = [];
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json' && 
                pathinfo($file, PATHINFO_BASENAME) !== 'changelog.json') {
                $data = json_decode(File::get($file), true);
                if (isset($data['id'], $data['timestamp'], $data['tables_count'])) {
                    $snapshots[] = [
                        'id' => $data['id'],
                        'timestamp' => $data['timestamp'],
                        'tables' => $data['tables_count'],
                        'created_at' => $data['created_at'] ?? 'Unknown',
                        'database' => $data['database'] ?? 'Unknown',
                    ];
                }
            }
        }
        
        // Sort by timestamp (newest first)
        usort($snapshots, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return $snapshots;
    }
    
    /**
     * Get changelog
     */
    protected function getChangelog()
    {
        $changelogPath = $this->storagePath . "/changelog.json";
        
        if (!File::exists($changelogPath)) {
            return [];
        }
        
        $changelog = json_decode(File::get($changelogPath), true) ?? [];
        
        // Show newest first
        return array_reverse($changelog);
    }
    
    /**
     * Get messages table status
     */
    protected function getMessagesTableStatus()
    {
        $status = [
            'exists' => Schema::hasTable('messages'),
            'issues' => []
        ];
        
        if (!$status['exists']) {
            return $status;
        }
        
        $status['row_count'] = DB::table('messages')->count();
        
        // Check for required columns
        $status['has_sender_type'] = Schema::hasColumn('messages', 'sender_type');
        $status['has_receiver_type'] = Schema::hasColumn('messages', 'receiver_type');
        
        // Check for NULL values in required columns
        $status['null_sender_ids'] = DB::table('messages')->whereNull('sender_id')->count();
        $status['null_receiver_ids'] = DB::table('messages')->whereNull('receiver_id')->count();
        
        // Check for NULL type values if columns exist
        if ($status['has_sender_type']) {
            $status['null_sender_types'] = DB::table('messages')->whereNull('sender_type')->count();
        }
        
        if ($status['has_receiver_type']) {
            $status['null_receiver_types'] = DB::table('messages')->whereNull('receiver_type')->count();
        }
        
        // Check for UTF-8 encoding issues
        if (DB::connection()->getDriverName() === 'mysql' || DB::connection()->getDriverName() === 'mariadb') {
            try {
                $status['invalid_utf8'] = DB::select("
                    SELECT COUNT(*) as count FROM messages 
                    WHERE message IS NOT NULL 
                    AND message <> '' 
                    AND message <> CONVERT(message USING utf8mb4)
                ")[0]->count;
            } catch (\Exception $e) {
                $status['utf8_check_error'] = $e->getMessage();
            }
        }
        
        // Check for orphaned messages
        $status['orphaned_senders'] = DB::table('messages')
            ->leftJoin('users', 'messages.sender_id', '=', 'users.id')
            ->whereNull('users.id')
            ->count();
        
        $status['orphaned_receivers'] = DB::table('messages')
            ->leftJoin('users', 'messages.receiver_id', '=', 'users.id')
            ->whereNull('users.id')
            ->count();
        
        // Compile issues
        if (!$status['has_sender_type']) {
            $status['issues'][] = 'Missing sender_type column';
        }
        
        if (!$status['has_receiver_type']) {
            $status['issues'][] = 'Missing receiver_type column';
        }
        
        if ($status['null_sender_ids'] > 0) {
            $status['issues'][] = $status['null_sender_ids'] . ' messages with NULL sender_id';
        }
        
        if ($status['null_receiver_ids'] > 0) {
            $status['issues'][] = $status['null_receiver_ids'] . ' messages with NULL receiver_id';
        }
        
        if (isset($status['null_sender_types']) && $status['null_sender_types'] > 0) {
            $status['issues'][] = $status['null_sender_types'] . ' messages with NULL sender_type';
        }
        
        if (isset($status['null_receiver_types']) && $status['null_receiver_types'] > 0) {
            $status['issues'][] = $status['null_receiver_types'] . ' messages with NULL receiver_type';
        }
        
        if (isset($status['invalid_utf8']) && $status['invalid_utf8'] > 0) {
            $status['issues'][] = $status['invalid_utf8'] . ' messages with invalid UTF-8 encoding';
        }
        
        if ($status['orphaned_senders'] > 0) {
            $status['issues'][] = $status['orphaned_senders'] . ' messages with non-existent sender users';
        }
        
        if ($status['orphaned_receivers'] > 0) {
            $status['issues'][] = $status['orphaned_receivers'] . ' messages with non-existent receiver users';
        }
        
        return $status;
    }
}
