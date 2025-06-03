<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ViewResidentController;
use App\Http\Controllers\ProfileImageController;
use App\Services\SentimentAnalysisService;
// Welcome page route - shows logo and redirects to login
Route::get('/', function () {
    return view('Welcome'); // Your welcome Blade file: resources/views/Welcome.blade.php
})->name('welcome');

// Login routes
Route::get('/login', function () {
    return view('login'); // Your login Blade file: resources/views/login.blade.php
})->name('login.form');

Route::post('/login', [AuthController::class, 'login'])->name('login.process');

// Profile image route - supports users, admins, and residents
Route::get('/profile-image/{id}', [ProfileImageController::class, 'getProfileImage'])->name('profile.image');

// API Routes for Image Upload and Fetch
Route::prefix('api/residents/{id}')->middleware('auth')->group(function () {
    // Upload profile image
    Route::post('/profile-image', [App\Http\Controllers\ResidentController::class, 'uploadProfileImage'])->name('api.residents.profile-image');
    
    // Upload ID images
    Route::post('/id-images', [App\Http\Controllers\ResidentController::class, 'uploadIDImages'])->name('api.residents.id-images');
    
    // Get profile image
    Route::get('/profile-image', [App\Http\Controllers\ResidentController::class, 'getProfileImage'])->name('api.residents.get-profile-image');
    
    // Get ID images
    Route::get('/id-images', [App\Http\Controllers\ResidentController::class, 'getIDImages'])->name('api.residents.get-id-images');
});

// API Routes for Complaints
Route::prefix('api/complaints')->group(function () {
    // Get complaint photo
    Route::get('{id}/photo', [App\Http\Controllers\ComplaintController::class, 'getPhoto'])->name('api.complaints.photo');
    
    // Get complaint video
    Route::get('{id}/video', [App\Http\Controllers\ComplaintController::class, 'getVideo'])->name('api.complaints.video');
});

// API Routes for Document Request Photos
Route::prefix('api/documents')->group(function () {
    // Business clearance photos
    Route::get('{id}/photo-store', [DocumentRequestController::class, 'getPhotoStore'])->name('api.documents.photo-store');
    
    // Renovation/Extension photos
    Route::get('{id}/photo-current-house', [DocumentRequestController::class, 'getPhotoCurrentHouse'])->name('api.documents.photo-current-house');
    Route::get('{id}/photo-renovation', [DocumentRequestController::class, 'getPhotoRenovation'])->name('api.documents.photo-renovation');
    Route::get('{id}/photo-proof', [DocumentRequestController::class, 'getPhotoProof'])->name('api.documents.photo-proof');
    
    // ID and additional images
    Route::get('{id}/valid-id-front', [DocumentRequestController::class, 'getValidIDFront'])->name('api.documents.valid-id-front');
    Route::get('{id}/valid-id-back', [DocumentRequestController::class, 'getValidIDBack'])->name('api.documents.valid-id-back');
    Route::get('{id}/image', [DocumentRequestController::class, 'getImage'])->name('api.documents.image');
    Route::get('{id}/image2', [DocumentRequestController::class, 'getImage2'])->name('api.documents.image2');
    Route::get('{id}/image3', [DocumentRequestController::class, 'getImage3'])->name('api.documents.image3');
});

// Dashboard routes
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('/dashboard/refresh', [DashboardController::class, 'refreshData'])->middleware('auth')->name('dashboard.refresh');

// Additional user page
Route::get('/user', function () {
    return view('user');
})->middleware('auth');
Route::get('/documentrequests', [DocumentRequestController::class, 'index'])->middleware('auth')->name('documentrequests.index');
// Document Request page route
Route::get('/documentrequest', [DocumentRequestController::class, 'index'])->name('documentrequest');

// Complaint page route
Route::get('/complaint', function () {
    return view('complaint');  // make sure this view exists in resources/views/complaint.blade.php
})->name('complaint');
// Show the "News" page
Route::get('/news', [App\Http\Controllers\NewsController::class, 'index'])->name('news');
Route::post('/news', [App\Http\Controllers\NewsController::class, 'store'])->name('news.store');
Route::get('/news/{id}', [App\Http\Controllers\NewsController::class, 'show'])->name('news.show');

// Show the "Announcements" page
Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->middleware('auth')->name('announcements');
Route::post('/announcements', [App\Http\Controllers\AnnouncementController::class, 'store'])->middleware('auth')->name('announcements.store');
Route::get('/announcements/{id}/edit', [App\Http\Controllers\AnnouncementController::class, 'edit'])->middleware('auth')->name('announcements.edit');
Route::put('/announcements/{id}', [App\Http\Controllers\AnnouncementController::class, 'update'])->middleware('auth')->name('announcements.update');
Route::delete('/announcements/{id}', [App\Http\Controllers\AnnouncementController::class, 'destroy'])->middleware('auth')->name('announcements.destroy');

// Show the "FAQs" page
Route::get('/faqs', [App\Http\Controllers\FaqController::class, 'index'])->middleware('auth')->name('faqs');
Route::post('/faqs', [App\Http\Controllers\FaqController::class, 'store'])->middleware('auth')->name('faqs.store');
Route::get('/faqs/{id}/edit', [App\Http\Controllers\FaqController::class, 'edit'])->middleware('auth')->name('faqs.edit');
Route::put('/faqs/{id}', [App\Http\Controllers\FaqController::class, 'update'])->middleware('auth')->name('faqs.update');
Route::delete('/faqs/{id}', [App\Http\Controllers\FaqController::class, 'destroy'])->middleware('auth')->name('faqs.destroy');

// Messages routes
Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->middleware('auth')->name('messages');
Route::get('/messages/search', [App\Http\Controllers\MessageController::class, 'searchUsers'])->middleware('auth')->name('messages.search');
Route::get('/messages/debug', [App\Http\Controllers\MessageController::class, 'debugUsers'])->middleware('auth')->name('messages.debug');
Route::get('/messages/unread-count', [App\Http\Controllers\MessageController::class, 'getUnreadCount'])->middleware('auth')->name('messages.unread-count');
Route::get('/messages/{userId}', [App\Http\Controllers\MessageController::class, 'show'])->middleware('auth')->name('messages.show');
Route::post('/messages', [App\Http\Controllers\MessageController::class, 'store'])->middleware('auth')->name('messages.store');

// Feedback routes
Route::get('/feedback', [App\Http\Controllers\FeedbackController::class, 'index'])->middleware('auth')->name('feedback');
Route::post('/feedback', [App\Http\Controllers\FeedbackController::class, 'store'])->middleware('auth')->name('feedback.store');
Route::get('/feedback/create', [App\Http\Controllers\FeedbackController::class, 'create'])->middleware('auth')->name('feedback.create');
Route::get('/feedback/{id}/edit', [App\Http\Controllers\FeedbackController::class, 'edit'])->middleware('auth')->name('feedback.edit');
Route::put('/feedback/{id}', [App\Http\Controllers\FeedbackController::class, 'update'])->middleware('auth')->name('feedback.update');
Route::delete('/feedback/{id}', [App\Http\Controllers\FeedbackController::class, 'destroy'])->middleware('auth')->name('feedback.destroy');
// Residents routes
Route::get('/residents', [App\Http\Controllers\ResidentController::class, 'index'])->middleware('auth')->name('residents');
Route::get('/residents/{id}', [App\Http\Controllers\ResidentController::class, 'show'])->middleware('auth')->name('residents.show');
Route::get('/residents/{id}/view', [ViewResidentController::class, 'show'])->middleware('auth')->name('residents.view');
Route::put('/residents/{id}', [App\Http\Controllers\ResidentController::class, 'update'])->middleware('auth')->name('residents.update');
Route::post('/residents/{id}/verify', [App\Http\Controllers\ViewResidentController::class, 'toggleVerification'])->middleware('auth')->name('residents.verify');
Route::delete('/residents/{id}', [App\Http\Controllers\ResidentController::class, 'destroy'])->middleware('auth')->name('residents.destroy');

// RESTful API routes for ID image management (BLOB storage in MySQL)
Route::post('/api/residents/{id}/id-images', [ViewResidentController::class, 'uploadIDImages'])->middleware('auth')->name('api.residents.id.upload');
Route::put('/api/residents/{id}/id-images', [ViewResidentController::class, 'updateIDImages'])->middleware('auth')->name('api.residents.id.update');
Route::get('/api/residents/{id}/id-images', [ViewResidentController::class, 'getIDImages'])->middleware('auth')->name('api.residents.id.get');
Route::get('/residents/{id}/id-image/{type}', [ViewResidentController::class, 'serveIDImage'])->middleware('auth')->name('residents.id.image');
Route::delete('/api/residents/{id}/id-images', [ViewResidentController::class, 'deleteIDImage'])->middleware('auth')->name('api.residents.id.delete');
// Admin routes
Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->middleware('auth')->name('admin');
Route::get('/admin/create', [App\Http\Controllers\AdminController::class, 'create'])->middleware('auth')->name('admin.create');
Route::post('/admin', [App\Http\Controllers\AdminController::class, 'store'])->middleware('auth')->name('admin.store');

// My Profile route - using different path to avoid conflicts
Route::get('/my-profile', [App\Http\Controllers\AdminController::class, 'profile'])->middleware('auth')->name('my.profile');

Route::get('/admin/{id}', [App\Http\Controllers\AdminController::class, 'show'])->middleware('auth')->name('admin.show');
Route::get('/admin/{id}/edit', [App\Http\Controllers\AdminController::class, 'edit'])->middleware('auth')->name('admin.edit');
Route::put('/admin/{id}', [App\Http\Controllers\AdminController::class, 'update'])->middleware('auth')->name('admin.update');
Route::delete('/admin/{id}', [App\Http\Controllers\AdminController::class, 'destroy'])->middleware('auth')->name('admin.destroy');

// Profile management routes
Route::post('/profile/upload', [App\Http\Controllers\AdminController::class, 'uploadProfile'])->middleware('auth')->name('profile.upload');
Route::put('/profile/update', [App\Http\Controllers\AdminController::class, 'updateProfile'])->middleware('auth')->name('profile.update');
Route::put('/password/update', [App\Http\Controllers\AdminController::class, 'updatePassword'])->middleware('auth')->name('password.update');

Route::get('/profile', function () {
    return view('profile');
})->middleware('auth')->name('profile');


Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Profile image route for serving BLOB data for users
Route::get('/profile-image/{id}', function($id) {
    try {
        // Connect directly to the database to retrieve the image
        $pdo = new PDO(
            "mysql:host=".config('database.connections.mysql.host').";dbname=".config('database.connections.mysql.database'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password')
        );
        
        $stmt = $pdo->prepare("SELECT profile FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !$user['profile']) {
            // Return a default image or 404
            abort(404);
        }
        
        // Get the image data
        $imageData = $user['profile'];
        
        // Detect the image type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
        
        // Return the image with appropriate headers
        return response($imageData)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=3600');
    } catch (\Exception $e) {
        return response("Error: " . $e->getMessage(), 500);
    }
})->name('profile.image');

// Profile image route for serving BLOB data for residents
Route::get('/resident-profile-image/{id}', function($id) {
    $resident = \App\Models\Resident::find($id);
    
    if (!$resident || !$resident->profile) {
        // Return a default image or 404
        abort(404);
    }
    
    // Assuming the profile column contains binary image data
    $imageData = $resident->profile;
    
    // Detect the image type (you might want to store this separately)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($imageData);
    
    return response($imageData)
        ->header('Content-Type', $mimeType)
        ->header('Cache-Control', 'public, max-age=3600');
})->name('resident.profile.image'); // Make sure the route is named)->name('profile.image');

// Resident profile image route for serving BLOB data 
Route::get('/resident-image/{id}', function($id) {
    $resident = \App\Models\Resident::find($id);
    
    if (!$resident || !$resident->profile) {
        // Return a default image or 404
        abort(404);
    }
    
    // Assuming the profile column contains binary image data
    $imageData = $resident->profile;
    
    // Detect the image type (you might want to store this separately)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($imageData);
    
    return response($imageData)
        ->header('Content-Type', $mimeType)
        ->header('Cache-Control', 'public, max-age=3600');
})->name('resident.image');


// Use the DashboardController if it's available and has functionality
Route::get('/documentrequest', [DocumentRequestController::class, 'index'])->middleware('auth')->name('documentrequest');
Route::get('/documentrequests/data', [DocumentRequestController::class, 'fetchData'])->middleware('auth')->name('documentrequests.data');
Route::get('/documentrequests/debug', [DocumentRequestController::class, 'debug'])->middleware('auth')->name('documentrequests.debug');

Route::get('/complaint', [ComplaintController::class, 'index'])->middleware('auth')->name('complaint');
Route::get('/complaints/data', [ComplaintController::class, 'fetchData'])->middleware('auth')->name('complaints.data');
Route::get('/complaints/last-update', [ComplaintController::class, 'getLastUpdate'])->middleware('auth')->name('complaints.last-update');

// Document request edit, update and delete routes
Route::get('/documentrequest/{id}/edit', [DocumentRequestController::class, 'edit'])->middleware('auth')->name('documentrequest.edit');
Route::match(['post', 'put'], '/documentrequest/{id}/update', [DocumentRequestController::class, 'update'])->middleware('auth')->name('documentrequest.update');
Route::delete('/documentrequest/{id}/delete', [DocumentRequestController::class, 'destroy'])->middleware('auth')->name('documentrequest.delete');

// Complaint edit, update and delete routes
Route::get('/complaint/{id}/edit', [ComplaintController::class, 'edit'])->name('complaint.edit');
Route::put('/complaint/{id}/update', [ComplaintController::class, 'update'])->name('complaint.update');
Route::delete('/complaint/{id}/delete', [ComplaintController::class, 'destroy'])->middleware('auth')->name('complaint.delete');
Route::post('/complaints/analyze-sentiments', [ComplaintController::class, 'analyzeSentiments'])->name('complaints.analyze-sentiments');

// API route for AJAX sentiment analysis
Route::post('/api/sentiment/analyze', function(Request $request) {
    try {
        $text = $request->input('text', '');
        $user_id = $request->input('user_id');

        \Illuminate\Support\Facades\Log::debug("Processing sentiment analysis", [
            'user_id' => $user_id,
            'text' => $text,
            'request' => $request->all()
        ]);

        // Only process if we have text
        if (empty($text)) {
            return response()->json([
                'sentiment' => 'neutral',
                'success' => true,
                'scores' => ['negative' => 0, 'positive' => 0, 'neutral' => 100],
                'matched_tokens' => ['negative' => [], 'positive' => [], 'neutral' => []],
                'message' => 'No text provided'
            ]);
        }

        $service = new SentimentAnalysisService();
        $result = $service->analyzeSentiment($text);

        \Illuminate\Support\Facades\Log::info("Sentiment analysis result", [
            'user_id' => $user_id,
            'text' => $text,
            'result' => $result
        ]);

        // Update complaint if user_id provided
        if ($user_id) {
            $complaint = \App\Models\ComplaintRequest::where('user_id', $user_id)->first();

            if ($complaint) {
                // Determine new status based on sentiment
                $newStatus = match ($result['sentiment']) {
                    'negative' => 'phase3', // Set to investigation for negative sentiment
                    'positive' => 'phase1', // Set to review for positive sentiment
                    default => 'phase2', // Set to additional requirements for neutral
                };

                // Only update status if it's in pending state
                if ($complaint->status === 'pending') {
                    $updated = $complaint->update([
                        'sentiment' => $result['sentiment'],
                        'status' => $newStatus
                    ]);
                } else {
                    $updated = $complaint->update(['sentiment' => $result['sentiment']]);
                }

                \Illuminate\Support\Facades\Log::info("Updated complaint", [
                    'user_id' => $user_id,
                    'sentiment' => $result['sentiment'],
                    'old_status' => $complaint->status,
                    'new_status' => $newStatus,
                    'status_updated' => $complaint->status === 'pending',
                    'success' => $updated
                ]);

                // Add status to result so frontend knows about the change
                $result['status'] = $complaint->status === 'pending' ? $newStatus : $complaint->status;
            } else {
                \Illuminate\Support\Facades\Log::error("Complaint not found", [
                    'user_id' => $user_id
                ]);
            }
        }

        // Return the complete analysis result
        return response()->json($result);

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Sentiment analysis error", [
            'error' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
            'user_id' => $user_id ?? null,
            'text' => $text ?? null
        ]);

        $errorMessage = config('app.debug') ? $e->getMessage() : 'Error analyzing sentiment';

        return response()->json([
            'sentiment' => 'neutral',
            'success' => false,
            'error' => $errorMessage
        ], 500);
    }
})->name('api.sentiment.analyze');

Route::get('/news/{id}/edit', [App\Http\Controllers\NewsController::class, 'edit'])->name('news.edit');
Route::put('/news/{id}/update', [App\Http\Controllers\NewsController::class, 'update'])->name('news.update');
Route::delete('/news/{id}/delete', [App\Http\Controllers\NewsController::class, 'destroy'])->name('news.delete');

// Live update routes have been disabled to stop auto-refresh functionality
// Route::get('/live-updates/stream', [App\Http\Controllers\LiveUpdateController::class, 'stream'])->middleware('auth')->name('live.stream');
// Route::post('/live-updates/trigger', [App\Http\Controllers\LiveUpdateController::class, 'triggerUpdate'])->middleware('auth')->name('live.trigger');

// Messages test route for debugging
Route::get('/messages/test', function() {
    try {
        // Check if the messages table exists
        $tableExists = \Illuminate\Support\Facades\Schema::hasTable('messages');
        
        // Get sample users for testing
        $users = \App\Models\User::take(3)->get(['id', 'name', 'email']);
        
        // Test database connection
        $dbConfig = config('database.connections.' . config('database.default'));
        unset($dbConfig['password']); // Remove password for security
        
        return response()->json([
            'success' => true,
            'message' => 'Debug information for messaging system',
            'auth_status' => [
                'authenticated' => auth()->check(),
                'user_id' => auth()->id(),
                'user_name' => auth()->user() ? auth()->user()->name : null,
            ],
            'messages_table_exists' => $tableExists,
            'sample_users' => $users,
            'database_config' => $dbConfig,
            'csrf_token_exists' => session()->has('_token'),
            'php_version' => phpversion(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
})->middleware('auth')->name('messages.test');

// Image serving routes for external files
Route::get('/residents/{id}/image/profile', [App\Http\Controllers\ResidentController::class, 'serveProfileImage'])->name('resident.profile.image');
Route::get('/residents/{id}/image/id/{type}', [App\Http\Controllers\ResidentController::class, 'serveIDImage'])->name('resident.id.image')->where('type', 'front|back');











