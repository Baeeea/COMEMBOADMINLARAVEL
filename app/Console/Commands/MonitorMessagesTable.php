<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;

class MonitorMessagesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:monitor-messages {--fix : Attempt to automatically fix issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor the messages table for common issues and fix them if needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking messages table for issues...');
        
        // Check if the messages table exists
        if (!Schema::hasTable('messages')) {
            $this->error('Messages table does not exist!');
            return 1;
        }
        
        $issues = [];
        $fixedIssues = [];
        
        // Check for required columns
        $senderTypeExists = Schema::hasColumn('messages', 'sender_type');
        $receiverTypeExists = Schema::hasColumn('messages', 'receiver_type');
        
        if (!$senderTypeExists) {
            $issues['missing_sender_type'] = 'The sender_type column is missing';
            
            if ($this->option('fix')) {
                try {
                    Schema::table('messages', function ($table) {
                        $table->string('sender_type')->default('App\\Models\\User')->after('sender_id');
                    });
                    $fixedIssues['missing_sender_type'] = 'Added sender_type column';
                } catch (\Exception $e) {
                    $this->error('Failed to add sender_type column: ' . $e->getMessage());
                }
            }
        }
        
        if (!$receiverTypeExists) {
            $issues['missing_receiver_type'] = 'The receiver_type column is missing';
            
            if ($this->option('fix')) {
                try {
                    Schema::table('messages', function ($table) {
                        $table->string('receiver_type')->default('App\\Models\\User')->after('receiver_id');
                    });
                    $fixedIssues['missing_receiver_type'] = 'Added receiver_type column';
                } catch (\Exception $e) {
                    $this->error('Failed to add receiver_type column: ' . $e->getMessage());
                }
            }
        }
        
        // Check for NULL values in required columns
        $nullSenderIds = DB::table('messages')->whereNull('sender_id')->count();
        if ($nullSenderIds > 0) {
            $issues['null_sender_ids'] = "Found {$nullSenderIds} messages with NULL sender_id";
        }
        
        $nullReceiverIds = DB::table('messages')->whereNull('receiver_id')->count();
        if ($nullReceiverIds > 0) {
            $issues['null_receiver_ids'] = "Found {$nullReceiverIds} messages with NULL receiver_id";
        }
        
        // Check for NULL type values if columns exist
        if ($senderTypeExists) {
            $nullSenderTypes = DB::table('messages')->whereNull('sender_type')->count();
            if ($nullSenderTypes > 0) {
                $issues['null_sender_types'] = "Found {$nullSenderTypes} messages with NULL sender_type";
                
                if ($this->option('fix')) {
                    try {
                        $updated = DB::table('messages')
                            ->whereNull('sender_type')
                            ->update(['sender_type' => 'App\\Models\\User']);
                        $fixedIssues['null_sender_types'] = "Updated {$updated} messages with default sender_type";
                    } catch (\Exception $e) {
                        $this->error('Failed to update NULL sender_type values: ' . $e->getMessage());
                    }
                }
            }
        }
        
        if ($receiverTypeExists) {
            $nullReceiverTypes = DB::table('messages')->whereNull('receiver_type')->count();
            if ($nullReceiverTypes > 0) {
                $issues['null_receiver_types'] = "Found {$nullReceiverTypes} messages with NULL receiver_type";
                
                if ($this->option('fix')) {
                    try {
                        $updated = DB::table('messages')
                            ->whereNull('receiver_type')
                            ->update(['receiver_type' => 'App\\Models\\User']);
                        $fixedIssues['null_receiver_types'] = "Updated {$updated} messages with default receiver_type";
                    } catch (\Exception $e) {
                        $this->error('Failed to update NULL receiver_type values: ' . $e->getMessage());
                    }
                }
            }
        }
        
        // Check for UTF-8 encoding issues
        if (DB::connection()->getDriverName() === 'mysql' || DB::connection()->getDriverName() === 'mariadb') {
            try {
                $invalidUtf8Count = DB::select("
                    SELECT COUNT(*) as count FROM messages 
                    WHERE message IS NOT NULL 
                    AND message <> '' 
                    AND message <> CONVERT(message USING utf8mb4)
                ")[0]->count;
                
                if ($invalidUtf8Count > 0) {
                    $issues['invalid_utf8'] = "Found {$invalidUtf8Count} messages with invalid UTF-8 encoding";
                    
                    if ($this->option('fix')) {
                        // Get the problematic rows
                        $invalidMessages = DB::select("
                            SELECT id, message FROM messages 
                            WHERE message IS NOT NULL 
                            AND message <> '' 
                            AND message <> CONVERT(message USING utf8mb4)
                        ");
                        
                        $fixed = 0;
                        foreach ($invalidMessages as $row) {
                            try {
                                // Try to sanitize the message text
                                $sanitized = mb_convert_encoding($row->message, 'UTF-8', 'UTF-8');
                                
                                DB::table('messages')
                                    ->where('id', $row->id)
                                    ->update(['message' => $sanitized]);
                                    
                                $fixed++;
                            } catch (\Exception $e) {
                                $this->warn("Could not fix UTF-8 encoding for message ID {$row->id}");
                            }
                        }
                        
                        $fixedIssues['invalid_utf8'] = "Fixed UTF-8 encoding for {$fixed} messages";
                    }
                }
            } catch (\Exception $e) {
                $this->warn('Could not check for UTF-8 encoding issues: ' . $e->getMessage());
            }
        }
        
        // Check for orphaned messages (referring to non-existent users)
        $orphanedSenders = DB::table('messages')
            ->leftJoin('users', 'messages.sender_id', '=', 'users.id')
            ->whereNull('users.id')
            ->count();
        
        $orphanedReceivers = DB::table('messages')
            ->leftJoin('users', 'messages.receiver_id', '=', 'users.id')
            ->whereNull('users.id')
            ->count();
        
        if ($orphanedSenders > 0) {
            $issues['orphaned_senders'] = "Found {$orphanedSenders} messages with non-existent sender users";
        }
        
        if ($orphanedReceivers > 0) {
            $issues['orphaned_receivers'] = "Found {$orphanedReceivers} messages with non-existent receiver users";
        }
        
        // Output results
        if (empty($issues)) {
            $this->info('✓ No issues found in the messages table!');
            return 0;
        }
        
        $this->warn('Issues detected in the messages table:');
        
        foreach ($issues as $key => $description) {
            $this->line(" - {$description}" . 
                (isset($fixedIssues[$key]) ? " => FIXED: {$fixedIssues[$key]}" : ''));
        }
        
        // If fixes were applied, re-check the remaining issues
        if ($this->option('fix') && !empty($fixedIssues)) {
            $this->info("\nRe-checking for remaining issues...");
            
            $remainingIssues = count($issues) - count($fixedIssues);
            if ($remainingIssues > 0) {
                $this->warn("{$remainingIssues} issues still need attention.");
                
                // Get the unfixed issues
                $unfixedIssues = array_diff_key($issues, $fixedIssues);
                foreach ($unfixedIssues as $key => $description) {
                    $this->line(" - {$description}");
                }
            } else {
                $this->info("✓ All issues have been resolved!");
            }
        } else if (!empty($issues)) {
            $this->info("\nRun this command with the --fix option to attempt automatic repairs.");
        }
        
        // Log issues to the Laravel log
        Log::channel('daily')->info('Messages table monitoring results', [
            'issues' => $issues,
            'fixed' => $fixedIssues,
            'timestamp' => Carbon::now()->toDateTimeString()
        ]);
        
        return !empty($issues) && empty($fixedIssues) ? 1 : 0;
    }
}
