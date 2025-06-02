<?php

namespace App\Http\Controllers;

use App\Models\ComplaintRequest;
use App\Models\Resident;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaintCount = ComplaintRequest::count();
        $complaints = ComplaintRequest::with(['resident:user_id,first_name,last_name,middle_name'])
            ->select(
                 'id', 'user_id', 'complaint_type', 'created_at', 'phase_status', 'sentiment',
                'explanation'
            )->get();

        // Analyze sentiments for complaints that need it
        $sentimentService = new SentimentAnalysisService();
        foreach ($complaints as $complaint) {
            if (!empty($complaint->explanation) && empty($complaint->sentiment)) {
                $result = $sentimentService->analyzeSentiment($complaint->explanation);

                // Update sentiment and determine appropriate phase_status
                $complaint->sentiment = $result['sentiment'];

                // Only update phase_status if it's pending or not set
                if (empty($complaint->phase_status) || $complaint->phase_status === 'pending') {
                    $complaint->phase_status = match ($result['sentiment']) {
                        'negative' => 'phase3', // Investigation needed
                        'positive' => 'phase1', // Standard review
                        default => 'phase2',    // Additional review needed
                    };
                }

                $complaint->save();
            }
        }

        return view('complaint', compact('complaintCount', 'complaints'));
    }

    public function fetchData(Request $request)
    {
        $status = $request->query('status');

        $query = ComplaintRequest::with(['resident:user_id,first_name,last_name,middle_name'])
            ->select(
                'id', 'user_id', 'complaint_type', 'created_at', 'phase_status', 'sentiment',
                'explanation'
            );

        if ($status && $status !== 'all') {
            $query->where('phase_status', $status);
        }

        $complaints = $query->orderBy('created_at', 'desc')->get();

        // Analyze sentiments for complaints that need it
        $sentimentService = new SentimentAnalysisService();
        foreach ($complaints as $complaint) {
            if (!empty($complaint->explanation)) {
                $result = $sentimentService->analyzeSentiment($complaint->explanation);

                // Always update sentiment to get the latest analysis
                $complaint->sentiment = $result['sentiment'];

                // Only update phase_status if it's pending or not set
                if (empty($complaint->phase_status) || $complaint->phase_status === 'pending') {
                    $complaint->phase_status = match ($result['sentiment']) {
                        'negative' => 'phase3', // Investigation needed
                        'positive' => 'phase1', // Standard review
                        default => 'phase2',    // Additional review needed
                    };
                }

                $complaint->save();
            }
        }

        return response()->json($complaints);
    }

    public function getLastUpdate()
    {
        $lastUpdate = ComplaintRequest::max('created_at');
        $complaintCount = ComplaintRequest::count();
        
        return response()->json([
            'last_update' => $lastUpdate,
            'complaint_count' => $complaintCount,
            'timestamp' => now()->toISOString()
        ]);
    }

    public function edit($id)
    {
        $complaint = ComplaintRequest::findOrFail($id);            // Analyze sentiment if explanation exists
            if (!empty($complaint->explanation)) {
                try {
                    $sentimentService = new SentimentAnalysisService();
                    $result = $sentimentService->analyzeSentiment($complaint->explanation);

                    Log::info('Sentiment analysis result', [
                        'complaint_id' => $complaint->user_id,
                        'result' => $result
                    ]);

                    // Update sentiment and determine appropriate status
                    if (isset($result['sentiment'])) {
                        $complaint->sentiment = $result['sentiment'];

                        // Determine urgency based on weighted words and phrases
                        $hasWeightedNegative = false;
                        $hasUrgentPhrases = false;

                        if (isset($result['matched_tokens']['negative'])) {
                            foreach ($result['matched_tokens']['negative'] as $token) {
                                if (strpos($token, 'Weighted:') !== false ||
                                    strpos($token, 'negative phrase') !== false) {
                                    $hasWeightedNegative = true;
                                    break;
                                }
                            }
                        }

                        // Only update phase_status if it's pending or not set
                        if (empty($complaint->phase_status) || $complaint->phase_status === 'pending') {
                            // Use enhanced phase_status determination
                            $complaint->phase_status = match (true) {
                                $hasWeightedNegative => 'phase3',    // Immediate investigation for weighted negatives
                                $result['sentiment'] === 'negative' => 'phase2', // Standard negative handling
                                $result['sentiment'] === 'positive' => 'phase1', // Positive feedback
                                default => 'phase2',                 // Default handling
                            };
                        }

                        $complaint->save();

                    Log::info("Complaint sentiment analyzed", [
                        'id' => $id,
                        'user_id' => $complaint->user_id,
                        'sentiment' => $result['sentiment'],
                        'phase_status' => $complaint->phase_status
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Error analyzing sentiment", [
                    'error' => $e->getMessage(),
                    'id' => $id,
                    'user_id' => $complaint->user_id ?? 'unknown'
                ]);
                // Continue without sentiment analysis if it fails
            }
        }

        return view('editcomplaint', compact('complaint'));
    }

    public function update(Request $request, $id)
    {
        try {
            $complaint = ComplaintRequest::findOrFail($id);

            // Validate the request
            $request->validate([
                'phase_status' => 'required|in:pending,phase1,phase2,phase3,phase4,phase5,completed,rejected',
                'explanation' => 'nullable|string|max:500',
                'complaint_type' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'date_occurrence' => 'nullable|date',
                'frequency' => 'nullable|string|max:100',
                'people_involved' => 'nullable|string',
                'location_occurrence' => 'nullable|string|max:200',
                'photo' => 'nullable|file|image|max:2048',
                'video' => 'nullable|file|mimes:mp4,avi,mov|max:10240',
                'phases' => 'nullable|string|max:100',
                // Type-specific fields
                'items_stolen' => 'nullable|string',
                'items_value' => 'nullable|string',
                'business_name' => 'nullable|string|max:200',
                'vehicle_details' => 'nullable|string'
            ]);

            // Analyze sentiment if description is updated
            $sentiment = $complaint->sentiment; // Keep existing sentiment by default
            if ($request->has('description') && $request->description) {
                $sentimentService = new SentimentAnalysisService();
                $result = $sentimentService->analyzeSentiment($request->description);
                $sentiment = $result['sentiment'];
                Log::info("Sentiment analysis result for complaint {$id}", [
                    'sentiment' => $sentiment,
                    'scores' => $result['scores'] ?? null,
                    'matched_tokens' => $result['matched_tokens'] ?? null
                ]);
            }

            // Determine phase_status based on sentiment
            $newPhaseStatus = match ($sentiment) {
                'negative' => 'phase3', // Set to investigation for negative sentiment
                'positive' => 'phase1', // Set to review for positive sentiment
                default => 'phase2', // Set to additional requirements for neutral
            };

            // Handle file uploads
            $photoData = $complaint->photo;
            $videoData = $complaint->video;

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                // Store directly as binary data in database
                $photoData = file_get_contents($photo->getPathname());
            }

            if ($request->hasFile('video')) {
                $video = $request->file('video');
                // Store directly as binary data in database
                $videoData = file_get_contents($video->getPathname());
            }

            // Update the editable fields with the new simplified structure
            $update = [
                'complaint_type' => $request->complaint_type,
                'description' => $request->description,
                'date_occurrence' => $request->date_occurrence,
                'frequency' => $request->frequency,
                'people_involved' => $request->people_involved,
                'location_occurrence' => $request->location_occurrence,
                'photo' => $photoData,
                'video' => $videoData,
                'phases' => $request->phases,
                'phase_status' => $request->phase_status,
                'explanation' => $request->explanation,
                'sentiment' => $sentiment,
                // Type-specific fields
                'items_stolen' => $request->items_stolen,
                'items_value' => $request->items_value,
                'business_name' => $request->business_name,
                'vehicle_details' => $request->vehicle_details
            ];

            Log::info("Updating complaint", [
                'id' => $id,
                'user_id' => $complaint->user_id,
                'update_data' => $update,
                'original_sentiment' => $complaint->sentiment,
                'new_sentiment' => $sentiment
            ]);

            $complaint->update($update);

        return redirect()->route('complaint')->with('success', 'Complaint updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating complaint: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating complaint: ' . $e->getMessage());
        }
    }

    /**
     * Analyze sentiment for all complaints
     */    public function analyzeSentiments()
    {
        try {
            $sentimentService = new SentimentAnalysisService();
            $complaints = ComplaintRequest::whereNotNull('explanation')
                ->where('explanation', '!=', '')
                ->get();

            $updatedCount = 0;
            foreach ($complaints as $complaint) {
                $result = $sentimentService->analyzeSentiment($complaint->explanation);

                // Update complaint with sentiment and determine phase_status based on sentiment
                $newPhaseStatus = match ($result['sentiment']) {
                    'negative' => 'phase3', // Set to investigation for negative sentiment
                    'positive' => 'phase1', // Set to review for positive sentiment
                    default => 'phase2', // Set to additional requirements for neutral
                };

                // Update the current complaint object directly
                $complaint->sentiment = $result['sentiment'];
                $complaint->phase_status = $newPhaseStatus;
                $updated = $complaint->save();
                if ($updated) {
                    $updatedCount++;
                }
            }

            $message = "Sentiment analysis completed for {$updatedCount} complaints.";

            // Check if request is AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'updated_count' => $updatedCount
                ]);
            }

            return redirect()->route('complaint')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error analyzing sentiments: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error analyzing sentiments. Please try again.'
                ], 500);
            }

            return redirect()->route('complaint')->with('error', 'Error analyzing sentiments.');
        }
    }

    public function destroy($id)
    {
        try {
            $complaint = ComplaintRequest::findOrFail($id);
            $complaint->delete();

            // Check if request is AJAX
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Complaint deleted successfully.']);
            }

            return redirect()->route('complaint')->with('success', 'Complaint deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error deleting complaint.'], 500);
            }

            return redirect()->route('complaint')->with('error', 'Error deleting complaint.');
        }
    }

    /**
     * Get the photo binary data for a complaint
     * 
     * @param int $id The complaint ID
     * @return \Illuminate\Http\Response
     */
    public function getPhoto($id)
    {
        try {
            $complaint = ComplaintRequest::findOrFail($id);
            
            // Check if photo exists
            if (empty($complaint->photo)) {
                return response()->json(['error' => 'No photo available'], 404);
            }
            
            // If photo is stored as a file path rather than blob
            if (is_string($complaint->photo) && !$this->isBinary($complaint->photo)) {
                if (Storage::disk('public')->exists($complaint->photo)) {
                    return response()->file(Storage::disk('public')->path($complaint->photo));
                } else {
                    return response()->json(['error' => 'Photo file not found'], 404);
                }
            }
            
            // If photo is stored as binary BLOB in database
            $contentType = $this->getImageMimeType($complaint->photo);
            return response($complaint->photo)
                ->header('Content-Type', $contentType ?: 'image/jpeg')
                ->header('Content-Disposition', 'inline; filename="complaint-photo-'.$id.'.jpg"');
        } catch (\Exception $e) {
            Log::error('Error retrieving complaint photo', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Error retrieving photo'], 500);
        }
    }

    /**
     * Get the video binary data for a complaint
     * 
     * @param int $id The complaint ID
     * @return \Illuminate\Http\Response
     */
    public function getVideo($id)
    {
        try {
            $complaint = ComplaintRequest::findOrFail($id);
            
            // Check if video exists
            if (empty($complaint->video)) {
                return response()->json(['error' => 'No video available'], 404);
            }
            
            // If video is stored as a file path rather than blob
            if (is_string($complaint->video) && !$this->isBinary($complaint->video)) {
                if (Storage::disk('public')->exists($complaint->video)) {
                    return response()->file(Storage::disk('public')->path($complaint->video));
                } else {
                    return response()->json(['error' => 'Video file not found'], 404);
                }
            }
            
            // If video is stored as binary BLOB in database
            return response($complaint->video)
                ->header('Content-Type', 'video/mp4')
                ->header('Content-Disposition', 'inline; filename="complaint-video-'.$id.'.mp4"');
        } catch (\Exception $e) {
            Log::error('Error retrieving complaint video', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Error retrieving video'], 500);
        }
    }

    /**
     * Helper function to check if a string is likely binary data
     * 
     * @param string $data The data to check
     * @return bool
     */
    private function isBinary($data)
    {
        if (empty($data)) {
            return false;
        }
        
        // Check if data is string and possibly binary
        if (!is_string($data)) {
            return true; // If not string, assume binary
        }
        
        // If data has path-like pattern, assume it's a path not binary
        if (preg_match('#^[a-zA-Z0-9_/\.-]+$#', $data) && strlen($data) < 255) {
            return false;
        }
        
        // Check for common binary data patterns
        $binary = false;
        for ($i = 0; $i < min(strlen($data), 50); $i++) {
            if (ord($data[$i]) < 32 && !in_array(ord($data[$i]), [9, 10, 13])) {
                $binary = true;
                break;
            }
        }
        
        return $binary;
    }
    
    /**
     * Helper function to determine the MIME type of an image from its binary data
     * 
     * @param string $data The binary image data
     * @return string|null The MIME type or null if not determinable
     */
    private function getImageMimeType($data)
    {
        if (empty($data)) {
            return null;
        }
        
        // Check the first few bytes to determine file type
        $signature = substr($data, 0, 12);
        
        if (substr($signature, 0, 2) === "\xFF\xD8") {
            return 'image/jpeg';
        } elseif (substr($signature, 0, 8) === "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") {
            return 'image/png';
        } elseif (substr($signature, 0, 4) === "GIF8") {
            return 'image/gif';
        } elseif (substr($signature, 0, 2) === "BM") {
            return 'image/bmp';
        } elseif (in_array(substr($signature, 0, 4), ['RIFF', "\x00\x00\x01\x00"])) {
            return 'image/webp';
        } elseif (substr($signature, 0, 4) === "WEBP") {
            return 'image/webp';
        }
        
        return 'application/octet-stream';
    }
}

