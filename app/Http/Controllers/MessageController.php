<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display the messaging interface
     */
    public function index()
    {
        // Get all users who can receive messages (excluding the current user)
        $users = User::where('id', '!=', Auth::id())->get();
        
        // Get conversations for the sidebar
        $conversations = $this->getConversations();
        
        // Default to first conversation if available
        $activeConversation = null;
        $messages = [];
        
        if (count($conversations) > 0) {
            $firstUser = $conversations->first();
            if ($firstUser) {
                $activeConversation = $firstUser;
                $messages = $this->getMessages($firstUser->id);
            }
        }
        
        return view('messages', compact('users', 'conversations', 'activeConversation', 'messages'));
    }    /**
     * Show conversation with a specific user
     */
    public function show($userId)
    {
        try {
            // Get user details
            $activeConversation = User::findOrFail($userId);
            
            // Get all users who can receive messages (for new message dropdown)
            $users = User::where('id', '!=', Auth::id())->get();
            
            // Get conversations for the sidebar
            $conversations = $this->getConversations();
            
            // Check if this is an AJAX request for new message polling
            if (request()->ajax() || request()->query('format') === 'json') {
                $lastCheckedTime = request()->query('last_checked', now()->subMinutes(5)->toDateTimeString());
                
                // Check for new messages
                $newMessages = Message::where('sender_id', $userId)
                    ->where('receiver_id', Auth::id())
                    ->where('created_at', '>', $lastCheckedTime)
                    ->exists();
                      
                return response()->json([
                    'newMessages' => $newMessages
                ], 200, ['Content-Type' => 'application/json;charset=UTF-8']);
            }
            
            // Get messages for the selected conversation
            $messages = $this->getMessages($userId);
            
            // Mark messages as read
            $this->markAsRead($userId);
            
            return view('messages', compact('users', 'conversations', 'activeConversation', 'messages'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error showing conversation:', [
                'error' => $e->getMessage(),
                'userId' => $userId
            ]);
            
            // Redirect to the messages index with an error
            return redirect()->route('messages')->with('error', 'User not found or conversation unavailable.');
        }
    }    /**
     * Send a new message
     */
    public function store(Request $request)
    {
        try {
            // Log the incoming request for debugging
            \Illuminate\Support\Facades\Log::debug('Message store request:', [
                'request_data' => $request->all(),
                'is_ajax' => $request->ajax(),
                'sender_id' => Auth::id(),
                'content_type' => $request->header('Content-Type'),
                'accept' => $request->header('Accept'),
            ]);
            // Validate request
            $validated = $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000',
                // We validate sender_id if present but will override it with Auth::id()
                'sender_id' => 'sometimes|numeric',
            ]);
            // Check receiver exists
            $receiver = User::find($request->receiver_id);
            if (!$receiver) {
                throw new \Exception("Receiver user not found");
            }
            
            // Prevent sending messages to yourself
            if ($receiver->id == Auth::id()) {
                throw new \Exception("You cannot send messages to yourself");
            }
            // Create message
            $message = new Message();
            
            // Ensure the sender is the authenticated user
            if (!Auth::check()) {
                throw new \Exception("You must be logged in to send messages");
            }
              // Sanitize message text to ensure valid UTF-8
            $sanitizedMessage = mb_convert_encoding($request->message, 'UTF-8', 'UTF-8');
            
            // Check schema to see if we need to set sender_type and receiver_type
            $hasSenderType = \Illuminate\Support\Facades\Schema::hasColumn('messages', 'sender_type');
            $hasReceiverType = \Illuminate\Support\Facades\Schema::hasColumn('messages', 'receiver_type');
            
            $message->sender_id = Auth::id();
            $message->receiver_id = $request->receiver_id;
            
            // Only set these fields if they exist in the schema
            if ($hasSenderType) {
                $message->sender_type = 'App\Models\User';
            }
            if ($hasReceiverType) {
                $message->receiver_type = 'App\Models\User';
            }
            
            $message->message = $sanitizedMessage;
            $message->is_read = false;
            $message->save();
            
            // Log success
            \Illuminate\Support\Facades\Log::info('Message sent successfully:', [
                'message_id' => $message->id,
                'from_user' => $message->sender_id, 
                'to_user' => $message->receiver_id,
                'content_length' => strlen($message->message)
            ]);
            
            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                // Ensure proper JSON encoding with UTF-8
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully!',
                    'data' => [
                        'message' => $message->load('sender', 'receiver')
                    ]
                ], 200, ['Content-Type' => 'application/json;charset=UTF-8']);
            }
            
            // Regular form submission
            return redirect()->route('messages.show', $request->receiver_id)
                ->with('success', 'Message sent successfully!');
                
        } catch (\Exception $e) {
            // Log error with detailed information
            \Illuminate\Support\Facades\Log::error('Error sending message:', [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'from_user' => Auth::id(),
                'to_user' => $request->receiver_id ?? 'unknown',
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message: ' . $e->getMessage(),
                    'error_details' => config('app.debug') ? [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ] : null
                ], 500, ['Content-Type' => 'application/json;charset=UTF-8']);
            }
            
            return redirect()->back()->with('error', 'Failed to send message. Please try again.');
        }
    }/**
     * Search for users to start a new conversation
     */
    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        
        // Debug information to check query and auth status
        \Log::debug('Search query: ' . $query);
        \Log::debug('User is authenticated: ' . (Auth::check() ? 'Yes' : 'No'));
        \Log::debug('Auth user ID: ' . (Auth::id() ?? 'Not logged in'));
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        // First, check if there are any users at all (for debugging)
        $totalUsersCount = User::count();
        \Log::debug('Total users in DB: ' . $totalUsersCount);
        
        try {
            // Simplified query first to test basic functionality
            $users = User::where('id', '!=', Auth::id())->get();
            
            if ($users->isEmpty()) {
                \Log::warning('No users found excluding current user');
                // Return all users except current as a fallback
                $users = User::all();
            } else {
                // Apply the search filter
                $users = $users->filter(function($user) use ($query) {
                    // Case-insensitive search in name or email
                    $name = strtolower($user->name ?? '');
                    $firstname = strtolower($user->firstname ?? '');
                    $lastname = strtolower($user->lastname ?? '');
                    $email = strtolower($user->email ?? '');
                    $fullname = strtolower(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
                    $searchTerm = strtolower($query);
                    
                    return 
                        str_contains($name, $searchTerm) ||
                        str_contains($firstname, $searchTerm) ||
                        str_contains($lastname, $searchTerm) ||
                        str_contains($email, $searchTerm) ||
                        str_contains($fullname, $searchTerm);
                });
            }
            
            \Log::debug('Matching users found: ' . count($users));
        } catch (\Exception $e) {
            \Log::error('Error searching users: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error searching for users',
                'details' => $e->getMessage()
            ], 500);
        }
        
        // Check if user already has conversations with these users
        try {
            $currentUserId = Auth::id();
            $existingConversations = Message::where('sender_id', $currentUserId)
                ->orWhere('receiver_id', $currentUserId)
                ->get(['sender_id', 'receiver_id'])
                ->map(function($message) use ($currentUserId) {
                    return $message->sender_id == $currentUserId ? $message->receiver_id : $message->sender_id;
                })
                ->unique()
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error checking existing conversations: ' . $e->getMessage());
            $existingConversations = [];
        }
          // Format user results
        try {
            $formattedUsers = $users->map(function($user) use ($existingConversations) {
                // Handle cases where firstname/lastname might be null
                $displayName = (!empty($user->firstname) && !empty($user->lastname))
                    ? $user->firstname . ' ' . $user->lastname 
                    : (!empty($user->name) ? $user->name : 'User ' . $user->id);
                
                return [
                    'id' => $user->id,
                    'name' => $displayName,
                    'email' => $user->email ?? 'No email',
                    'avatar' => "https://ui-avatars.com/api/?name=" . urlencode($displayName),
                    'hasExistingConversation' => in_array($user->id, $existingConversations)
                ];
            })->take(15)->values(); // Limit to 15 results and reset indices
            
            \Log::debug('Formatted users: ' . $formattedUsers->count());
            return response()->json($formattedUsers);
        } catch (\Exception $e) {
            \Log::error('Error formatting users: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error processing user data',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get list of users the current user has conversations with
     */    private function getConversations()
    {
        $userId = Auth::id();
        if (!$userId) {
            return collect(); // Return empty collection if user is not authenticated
        }
        
        // Find all users that the current user has exchanged messages with
        $userIds = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get(['sender_id', 'receiver_id'])
            ->map(function ($message) use ($userId) {
                // For each message, return the ID of the other user
                return $message->sender_id == $userId 
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->filter() // Filter out any null values
            ->unique()
            ->values();
          // Get the user objects, only if we have user IDs
        if ($userIds->isEmpty()) {
            return collect(); // Return empty collection if no conversations
        }
        
        $users = User::whereIn('id', $userIds)->get();
        
        // Calculate unread count for each user
        foreach ($users as $user) {
            if ($user && $user->id) {
                $user->unread_count = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();
            } else {
                $user->unread_count = 0;
            }
        }
        
        return $users;
    }
    
    /**
     * Get messages between current user and specified user
     */    private function getMessages($otherUserId)
    {
        if (!$otherUserId) {
            return collect(); // Return empty collection if otherUserId is null
        }
        
        $userId = Auth::id();
        
        return Message::where(function ($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $otherUserId);
            })
            ->orWhere(function ($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $otherUserId)
                    ->where('receiver_id', $userId);
            })
            ->orderBy('created_at')
            ->with(['sender', 'receiver'])
            ->get();
    }
    
    /**
     * Mark all messages from a user as read
     */
    private function markAsRead($senderId)
    {
        Message::where('sender_id', $senderId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
    
    /**
     * Get count of unread messages for notification badge
     */
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count], 200, ['Content-Type' => 'application/json;charset=UTF-8']);
    }
    
    /**
     * Debug method to check users and message system
     */
    public function debugUsers(Request $request)
    {
        try {
            // Get basic user counts
            $totalUsers = User::count();
            $currentUserId = Auth::id();
            $currentUser = Auth::user();
            
            // Get sample users
            $otherUsers = User::where('id', '!=', $currentUserId)
                ->take(5)
                ->get(['id', 'name', 'email', 'firstname', 'lastname']);
                
            // Count messages
            $totalMessages = Message::count();
            $sentMessages = Message::where('sender_id', $currentUserId)->count();
            $receivedMessages = Message::where('receiver_id', $currentUserId)->count();
            
            // Test message insert
            $testResult = null;
            if ($request->has('test_insert') && count($otherUsers) > 0) {                $testUser = $otherUsers->first();
                $testMessage = new Message();
                $testMessage->sender_id = $currentUserId;
                $testMessage->receiver_id = $testUser->id;
                $testMessage->sender_type = 'App\Models\User';
                $testMessage->receiver_type = 'App\Models\User';
                $testMessage->message = 'Test message from debug endpoint: ' . now();
                $testMessage->is_read = false;
                $testMessage->save();
                
                $testResult = [
                    'success' => true,
                    'message_id' => $testMessage->id,
                    'to_user' => $testUser->name ?? $testUser->firstname.' '.$testUser->lastname
                ];
            }
            
            return response()->json([
                'success' => true,
                'current_user' => [
                    'id' => $currentUserId,
                    'name' => $currentUser->name,
                    'email' => $currentUser->email,
                    'full_name' => $currentUser->firstname && $currentUser->lastname 
                        ? $currentUser->firstname . ' ' . $currentUser->lastname 
                        : $currentUser->name
                ],
                'users' => [
                    'total' => $totalUsers,
                    'sample' => $otherUsers
                ],
                'messages' => [
                    'total' => $totalMessages,
                    'sent' => $sentMessages,
                    'received' => $receivedMessages
                ],
                'test_insert' => $testResult,
                'database' => [
                    'connection' => config('database.default'),
                    'messages_table_exists' => \Illuminate\Support\Facades\Schema::hasTable('messages'),
                    'users_table_exists' => \Illuminate\Support\Facades\Schema::hasTable('users')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
