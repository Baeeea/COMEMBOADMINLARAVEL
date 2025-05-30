<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class TrackDatabaseChanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:track-changes 
                            {--compare= : Compare with a previous snapshot (provide snapshot ID)}
                            {--scan : Scan all tables for changes}
                            {--history : Show all tracked changes history}
                            {--list : List all available snapshots}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track and log database schema changes over time';

    /**
     * The storage path for snapshots
     * 
     * @var string
     */
    protected $storagePath;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->storagePath = storage_path('app/db-snapshots');
        
        if (!File::exists($this->storagePath)) {
            File::makeDirectory($this->storagePath, 0755, true);
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('list')) {
            $this->listSnapshots();
            return 0;
        }

        if ($this->option('history')) {
            $this->displayHistory();
            return 0;
        }

        if ($compareId = $this->option('compare')) {
            $this->compareWithSnapshot($compareId);
            return 0;
        }

        // Take a new snapshot by default
        $this->takeSnapshot($this->option('scan'));
        return 0;
    }

    /**
     * Take a snapshot of the current database schema
     */
    protected function takeSnapshot(bool $fullScan = false)
    {
        $this->info('Taking database schema snapshot...');
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $snapshotId = uniqid($timestamp . '_');
        
        $tables = $this->getTables();
        $snapshot = [];
        $progress = $this->output->createProgressBar(count($tables));
        $progress->start();

        foreach ($tables as $table) {
            $columns = DB::select("SHOW COLUMNS FROM `{$table}`");
            $indexes = $this->getIndexes($table);
            $foreignKeys = $this->getForeignKeys($table);
            $rowCount = DB::table($table)->count();
            
            // For messages table, also check for specific issues
            $specificIssues = [];
            if ($table === 'messages' || $fullScan) {
                $specificIssues = $this->checkTableIssues($table);
            }
            
            $snapshot[$table] = [
                'columns' => $columns,
                'indexes' => $indexes,
                'foreign_keys' => $foreignKeys,
                'row_count' => $rowCount,
                'specific_issues' => $specificIssues
            ];
            
            $progress->advance();
        }
        
        $progress->finish();
        $this->newLine();
        
        $snapshotData = [
            'id' => $snapshotId,
            'timestamp' => $timestamp,
            'database' => DB::connection()->getDatabaseName(),
            'tables' => $snapshot,
            'tables_count' => count($tables),
            'created_at' => Carbon::now()->toDateTimeString(),
            'mysql_version' => $this->getMySQLVersion(),
            'laravel_version' => app()->version(),
        ];
        
        $filePath = $this->storagePath . "/{$snapshotId}.json";
        File::put($filePath, json_encode($snapshotData, JSON_PRETTY_PRINT));
        
        $this->createChangeLogEntry($snapshotId, 'snapshot', 'Created new database snapshot');
        
        $this->info("Snapshot taken successfully! ID: {$snapshotId}");
        $this->info("Stored at: {$filePath}");
        
        return $snapshotId;
    }
    
    /**
     * Get all database tables
     */
    protected function getTables()
    {
        return DB::select('SHOW TABLES');
    }
    
    /**
     * Get table indexes
     */
    protected function getIndexes($table)
    {
        return DB::select("SHOW INDEXES FROM `{$table}`");
    }
    
    /**
     * Get foreign keys for a table
     */
    protected function getForeignKeys($table)
    {
        $foreignKeys = [];
        
        try {
            $schema = DB::connection()->getDatabaseName();
            
            $keys = DB::select("
                SELECT 
                    COLUMN_NAME as column_name,
                    REFERENCED_TABLE_NAME as referenced_table,
                    REFERENCED_COLUMN_NAME as referenced_column
                FROM 
                    information_schema.KEY_COLUMN_USAGE
                WHERE 
                    TABLE_SCHEMA = '{$schema}' AND
                    TABLE_NAME = '{$table}' AND
                    REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            $foreignKeys = $keys;
        } catch (\Exception $e) {
            $this->warn("Could not retrieve foreign keys for table '{$table}': " . $e->getMessage());
        }
        
        return $foreignKeys;
    }
    
    /**
     * Get MySQL version
     */
    protected function getMySQLVersion()
    {
        try {
            $result = DB::select('SELECT VERSION() as version');
            return $result[0]->version ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Could not determine version';
        }
    }
    
    /**
     * Compare current schema with a previous snapshot
     */
    protected function compareWithSnapshot($snapshotId)
    {
        $snapshotPath = $this->storagePath . "/{$snapshotId}.json";
        
        if (!File::exists($snapshotPath)) {
            $this->error("Snapshot with ID {$snapshotId} not found!");
            return;
        }
        
        $this->info("Comparing current database schema with snapshot: {$snapshotId}");
        
        // Load previous snapshot
        $previousSnapshot = json_decode(File::get($snapshotPath), true);
        
        // Take new snapshot for comparison
        $currentSnapshotId = $this->takeSnapshot(true);
        $currentSnapshotPath = $this->storagePath . "/{$currentSnapshotId}.json";
        $currentSnapshot = json_decode(File::get($currentSnapshotPath), true);
        
        $changes = [];
        
        // Compare tables
        $previousTables = array_keys($previousSnapshot['tables']);
        $currentTables = array_keys($currentSnapshot['tables']);
        
        $addedTables = array_diff($currentTables, $previousTables);
        $removedTables = array_diff($previousTables, $currentTables);
        
        if (count($addedTables) > 0) {
            $changes['added_tables'] = $addedTables;
        }
        
        if (count($removedTables) > 0) {
            $changes['removed_tables'] = $removedTables;
        }
        
        // Compare table structures
        $commonTables = array_intersect($currentTables, $previousTables);
        $tableChanges = [];
        
        foreach ($commonTables as $table) {
            $tableChange = $this->compareTable(
                $table,
                $previousSnapshot['tables'][$table], 
                $currentSnapshot['tables'][$table]
            );
            
            if (!empty($tableChange)) {
                $tableChanges[$table] = $tableChange;
            }
        }
        
        if (count($tableChanges) > 0) {
            $changes['table_changes'] = $tableChanges;
        }
        
        // Output results
        if (empty($changes)) {
            $this->info("No schema changes detected between current state and snapshot {$snapshotId}.");
            return;
        }
        
        $this->info("Changes detected:");
        
        if (isset($changes['added_tables'])) {
            $this->info("Added tables:");
            foreach ($changes['added_tables'] as $table) {
                $this->line(" - {$table}");
            }
        }
        
        if (isset($changes['removed_tables'])) {
            $this->info("Removed tables:");
            foreach ($changes['removed_tables'] as $table) {
                $this->line(" - {$table}");
            }
        }
        
        if (isset($changes['table_changes'])) {
            $this->info("Modified tables:");
            foreach ($changes['table_changes'] as $table => $tableChanges) {
                $this->line(" - {$table}");
                
                if (isset($tableChanges['added_columns'])) {
                    $this->line("   Added columns:");
                    foreach ($tableChanges['added_columns'] as $column) {
                        $this->line("    * {$column->Field} ({$column->Type})");
                    }
                }
                
                if (isset($tableChanges['removed_columns'])) {
                    $this->line("   Removed columns:");
                    foreach ($tableChanges['removed_columns'] as $column) {
                        $this->line("    * {$column->Field}");
                    }
                }
                
                if (isset($tableChanges['modified_columns'])) {
                    $this->line("   Modified columns:");
                    foreach ($tableChanges['modified_columns'] as $columnName => $change) {
                        $this->line("    * {$columnName}: {$change['old']} => {$change['new']}");
                    }
                }
                
                if (isset($tableChanges['row_count_change'])) {
                    $this->line("   Row count: {$tableChanges['row_count_change']['old']} => {$tableChanges['row_count_change']['new']} (" . 
                        ($tableChanges['row_count_change']['new'] - $tableChanges['row_count_change']['old']) . " " . 
                        ($tableChanges['row_count_change']['new'] > $tableChanges['row_count_change']['old'] ? "added" : "removed") . ")");
                }
                
                if (isset($tableChanges['specific_issues'])) {
                    $this->line("   Issues detected:");
                    foreach ($tableChanges['specific_issues'] as $issue => $details) {
                        if (is_array($details)) {
                            $this->line("    * {$issue}: {$details['old']} => {$details['new']}");
                        } else {
                            $this->line("    * {$issue}: {$details}");
                        }
                    }
                }
            }
        }
        
        // Save comparison results to changelog
        $this->createChangeLogEntry(
            $currentSnapshotId, 
            'comparison', 
            "Compared current schema with snapshot {$snapshotId}",
            $changes
        );
    }
    
    /**
     * Check for specific issues in tables, especially messages table
     */
    protected function checkTableIssues($table)
    {
        $issues = [];
        
        try {
            if ($table === 'messages') {
                // Check for null values in critical columns
                $nullSenderIds = DB::table('messages')->whereNull('sender_id')->count();
                $nullReceiverIds = DB::table('messages')->whereNull('receiver_id')->count();
                
                if ($nullSenderIds > 0) {
                    $issues['null_sender_ids'] = $nullSenderIds;
                }
                
                if ($nullReceiverIds > 0) {
                    $issues['null_receiver_ids'] = $nullReceiverIds;
                }
                
                // Check for sender_type and receiver_type columns
                $senderTypeExists = Schema::hasColumn('messages', 'sender_type');
                $receiverTypeExists = Schema::hasColumn('messages', 'receiver_type');
                
                if (!$senderTypeExists) {
                    $issues['missing_sender_type'] = true;
                } else {
                    $nullSenderTypes = DB::table('messages')->whereNull('sender_type')->count();
                    if ($nullSenderTypes > 0) {
                        $issues['null_sender_types'] = $nullSenderTypes;
                    }
                }
                
                if (!$receiverTypeExists) {
                    $issues['missing_receiver_type'] = true;
                } else {
                    $nullReceiverTypes = DB::table('messages')->whereNull('receiver_type')->count();
                    if ($nullReceiverTypes > 0) {
                        $issues['null_receiver_types'] = $nullReceiverTypes;
                    }
                }
                
                // Check for UTF-8 encoding issues in message content
                if (DB::connection()->getDriverName() === 'mysql') {
                    try {
                        $invalidUtf8Count = DB::select("
                            SELECT COUNT(*) as count FROM messages 
                            WHERE message IS NOT NULL 
                            AND message <> '' 
                            AND message <> CONVERT(message USING utf8mb4)
                        ")[0]->count;
                        
                        if ($invalidUtf8Count > 0) {
                            $issues['invalid_utf8_messages'] = $invalidUtf8Count;
                        }
                    } catch (\Exception $e) {
                        $issues['utf8_check_error'] = $e->getMessage();
                    }
                }
                
                // Check for orphaned records (referring to non-existent users)
                $orphanedSenders = DB::table('messages')
                    ->leftJoin('users', 'messages.sender_id', '=', 'users.id')
                    ->whereNull('users.id')
                    ->count();
                
                $orphanedReceivers = DB::table('messages')
                    ->leftJoin('users', 'messages.receiver_id', '=', 'users.id')
                    ->whereNull('users.id')
                    ->count();
                
                if ($orphanedSenders > 0) {
                    $issues['orphaned_sender_messages'] = $orphanedSenders;
                }
                
                if ($orphanedReceivers > 0) {
                    $issues['orphaned_receiver_messages'] = $orphanedReceivers;
                }
            }
            
            // Generic checks for any table
            $nullColumnCounts = [];
            $columns = Schema::getColumnListing($table);
            
            foreach ($columns as $column) {
                $nullCount = DB::table($table)->whereNull($column)->count();
                if ($nullCount > 0) {
                    $nullColumnCounts[$column] = $nullCount;
                }
            }
            
            if (!empty($nullColumnCounts)) {
                $issues['null_values'] = $nullColumnCounts;
            }
            
        } catch (\Exception $e) {
            $issues['check_error'] = $e->getMessage();
        }
        
        return $issues;
    }
    
    /**
     * Compare two table structures
     */
    protected function compareTable($tableName, $oldTable, $newTable)
    {
        $changes = [];
        
        // Compare columns
        $oldColumns = collect($oldTable['columns'])->keyBy('Field');
        $newColumns = collect($newTable['columns'])->keyBy('Field');
        
        $addedColumns = $newColumns->diffKeys($oldColumns);
        $removedColumns = $oldColumns->diffKeys($newColumns);
        
        if ($addedColumns->count() > 0) {
            $changes['added_columns'] = $addedColumns->values()->all();
        }
        
        if ($removedColumns->count() > 0) {
            $changes['removed_columns'] = $removedColumns->values()->all();
        }
        
        // Check for modified columns
        $modifiedColumns = [];
        foreach ($oldColumns as $name => $oldColumn) {
            if ($newColumns->has($name)) {
                $newColumn = $newColumns[$name];
                
                $modifications = [];
                
                // Check type changes
                if ($oldColumn->Type !== $newColumn->Type) {
                    $modifications['type'] = [
                        'old' => $oldColumn->Type,
                        'new' => $newColumn->Type
                    ];
                }
                
                // Check nullable changes
                if ($oldColumn->Null !== $newColumn->Null) {
                    $modifications['nullable'] = [
                        'old' => $oldColumn->Null,
                        'new' => $newColumn->Null
                    ];
                }
                
                // Check default value changes
                if ($oldColumn->Default !== $newColumn->Default) {
                    $modifications['default'] = [
                        'old' => $oldColumn->Default ?? 'NULL',
                        'new' => $newColumn->Default ?? 'NULL'
                    ];
                }
                
                if (!empty($modifications)) {
                    $modifiedColumns[$name] = $modifications;
                }
            }
        }
        
        if (!empty($modifiedColumns)) {
            $changes['modified_columns'] = $modifiedColumns;
        }
        
        // Compare row count
        if ($oldTable['row_count'] !== $newTable['row_count']) {
            $changes['row_count_change'] = [
                'old' => $oldTable['row_count'],
                'new' => $newTable['row_count']
            ];
        }
        
        // Compare specific issues for messages table
        if ($tableName === 'messages' && !empty($oldTable['specific_issues']) && !empty($newTable['specific_issues'])) {
            $issueChanges = [];
            $allIssueKeys = array_unique(array_merge(
                array_keys($oldTable['specific_issues']),
                array_keys($newTable['specific_issues'])
            ));
            
            foreach ($allIssueKeys as $issue) {
                $oldValue = $oldTable['specific_issues'][$issue] ?? null;
                $newValue = $newTable['specific_issues'][$issue] ?? null;
                
                if ($oldValue !== $newValue) {
                    if (is_numeric($oldValue) && is_numeric($newValue)) {
                        $issueChanges[$issue] = [
                            'old' => $oldValue,
                            'new' => $newValue
                        ];
                    } else {
                        // If values aren't numeric, just note the change
                        $issueChanges[$issue] = "Changed";
                    }
                }
            }
            
            if (!empty($issueChanges)) {
                $changes['specific_issues'] = $issueChanges;
            }
        }
        
        return $changes;
    }
    
    /**
     * Create changelog entry
     */
    protected function createChangeLogEntry($snapshotId, $type, $description, $details = null)
    {
        $changelogPath = $this->storagePath . "/changelog.json";
        
        $changelog = [];
        if (File::exists($changelogPath)) {
            $changelog = json_decode(File::get($changelogPath), true) ?? [];
        }
        
        $entry = [
            'id' => uniqid(),
            'snapshot_id' => $snapshotId,
            'type' => $type,
            'description' => $description,
            'timestamp' => Carbon::now()->toDateTimeString(),
            'user' => getenv('USERNAME') ?: 'system'
        ];
        
        if ($details) {
            $entry['details'] = $details;
        }
        
        $changelog[] = $entry;
        
        File::put($changelogPath, json_encode($changelog, JSON_PRETTY_PRINT));
    }
    
    /**
     * List all available snapshots
     */
    protected function listSnapshots()
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
                        'created_at' => $data['created_at'] ?? 'Unknown'
                    ];
                }
            }
        }
        
        // Sort by timestamp (newest first)
        usort($snapshots, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        $this->table(
            ['ID', 'Timestamp', 'Tables Count', 'Created At'],
            array_map(function($snapshot) {
                return [
                    $snapshot['id'],
                    $snapshot['timestamp'],
                    $snapshot['tables'],
                    $snapshot['created_at']
                ];
            }, $snapshots)
        );
        
        $this->info("Total snapshots: " . count($snapshots));
    }
    
    /**
     * Display changelog history
     */
    protected function displayHistory()
    {
        $changelogPath = $this->storagePath . "/changelog.json";
        
        if (!File::exists($changelogPath)) {
            $this->info("No change history found.");
            return;
        }
        
        $changelog = json_decode(File::get($changelogPath), true) ?? [];
        
        // Show newest first
        $changelog = array_reverse($changelog);
        
        $this->table(
            ['ID', 'Type', 'Description', 'Snapshot ID', 'Timestamp', 'User'],
            array_map(function($entry) {
                return [
                    $entry['id'],
                    $entry['type'],
                    $entry['description'],
                    $entry['snapshot_id'],
                    $entry['timestamp'],
                    $entry['user']
                ];
            }, $changelog)
        );
        
        $this->info("Total entries: " . count($changelog));
    }
}
