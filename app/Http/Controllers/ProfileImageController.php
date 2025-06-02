<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProfileImageController extends Controller
{
    /**
     * Retrieve and serve a user's profile image
     * 
     * @param int $id The user ID
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getProfileImage($id, Request $request)
    {
        // Get the type of user (default to 'user')
        $type = $request->query('type', 'user');
        
        // Determine which model to use based on type
        switch ($type) {
            case 'admin':
                $model = \App\Models\Admin::class;
                break;
            case 'resident':
                $model = \App\Models\Resident::class;
                break;
            case 'user':
            default:
                $model = User::class;
        }
        
        // Find the user
        $user = $model::find($id);
        
        // Check if user exists and has a profile image
        if (!$user || empty($user->profile)) {
            return response()->json(['error' => 'Profile image not found.'], 404);
        }

        // Debug mode
        $debug = $request->query('debug') == '1';
        
        // Detect MIME type for proper content type header
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($user->profile);
        
        if ($debug) {
            error_log("Image MIME type detected: $mimeType");
            error_log("Profile data size: " . strlen($user->profile) . " bytes");
        }

        // Set appropriate headers
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="profile-'.$id.'.jpg"',
        ];        // Generate ETag based on user's updated_at timestamp and content for better cache invalidation
        $lastModified = $user->updated_at ? $user->updated_at->timestamp : time();
        $etag = md5($user->profile . $lastModified);
        $headers['ETag'] = '"' . $etag . '"';
        
        // Check if browser cache is still valid
        $ifNoneMatch = $request->header('If-None-Match');
        if ($ifNoneMatch && trim($ifNoneMatch, '"') === $etag) {
            return response()->make('', 304, $headers);
        }
        
        // Set caching headers based on debug mode
        if (!$debug) {
            // Enable caching for production
            $headers['Cache-Control'] = 'public, max-age=86400'; // Cache for 1 day
        } else {
            // Disable caching for debugging
            $headers['Cache-Control'] = 'no-cache, no-store, must-revalidate';
            $headers['Pragma'] = 'no-cache';
            $headers['Expires'] = '0';
        }
        
        // Serve the image data
        return Response::make($user->profile, 200, $headers);
    }
}
