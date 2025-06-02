<?php

namespace App\Http\Controllers;

use App\Models\ComplaintRequest;
use App\Models\Resident;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ComplaintController extends Controller
{
    private function triggerLiveUpdate()
    {
        Cache::put('last_database_update', time(), 3600);
    }
    public function index()
    {
        $complaintCount = ComplaintRequest::count();
        $complaints = ComplaintRequest::with(['resident:user_id,first_name,last_name,middle_name'])
            ->select(
                'user_id', 'complaint_type', 'created_at', 'phase_status', 'sentiment',
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
                'user_id', 'complaint_type', 'created_at', 'phase_status', 'sentiment',
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

    public function edit($user_id)
    {
        $complaint = ComplaintRequest::where('user_id', $user_id)->firstOrFail();            // Analyze sentiment if explanation exists
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
                        'user_id' => $user_id,
                        'sentiment' => $result['sentiment'],
                        'phase_status' => $complaint->phase_status
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Error analyzing sentiment", [
                    'error' => $e->getMessage(),
                    'user_id' => $user_id
                ]);
                // Continue without sentiment analysis if it fails
            }
        }

        return view('editcomplaint', compact('complaint'));
    }

    public function update(Request $request, $user_id)
    {
        try {
            $complaint = ComplaintRequest::where('user_id', $user_id)->firstOrFail();

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
                'phases' => 'nullable|string|max:100'
            ]);

            // Analyze sentiment if description is updated
            $sentiment = $complaint->sentiment; // Keep existing sentiment by default
            if ($request->has('description') && $request->description) {
                $sentimentService = new SentimentAnalysisService();
                $result = $sentimentService->analyzeSentiment($request->description);
                $sentiment = $result['sentiment'];
                Log::info("Sentiment analysis result for user_id {$user_id}", [
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
            $photoPath = $complaint->photo;
            $videoPath = $complaint->video;

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = $photo->store('complaint_photos', 'public');
            }

            if ($request->hasFile('video')) {
                $video = $request->file('video');
                $videoPath = $video->store('complaint_videos', 'public');
            }

            // Update the editable fields with the new simplified structure
            $update = [
                'complaint_type' => $request->complaint_type,
                'description' => $request->description,
                'date_occurrence' => $request->date_occurrence,
                'frequency' => $request->frequency,
                'people_involved' => $request->people_involved,
                'location_occurrence' => $request->location_occurrence,
                'photo' => $photoPath,
                'video' => $videoPath,
                'phases' => $request->phases,
                'phase_status' => $request->phase_status,
                'explanation' => $request->explanation,
                'sentiment' => $sentiment
            ];

            Log::info("Updating complaint", [
                'user_id' => $user_id,
                'update_data' => $update,
                'original_sentiment' => $complaint->sentiment,
                'new_sentiment' => $sentiment
            ]);

            $complaint->update($update);

            $this->triggerLiveUpdate();
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

                $updated = ComplaintRequest::where('user_id', $complaint->user_id)
                    ->update([
                        'sentiment' => $result['sentiment'],
                        'phase_status' => $newPhaseStatus
                    ]);
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

    public function destroy($user_id)
    {
        try {
            $complaint = ComplaintRequest::where('user_id', $user_id)->firstOrFail();
            $complaint->delete();

            $this->triggerLiveUpdate();

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
}

