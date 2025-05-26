<?php

namespace App\Http\Controllers;

use App\Models\ComplaintRequest;
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
        $complaints = ComplaintRequest::select(
            'user_id', 'firstname', 'lastname', 'middle_name',
            'complaint_type', 'timestamp', 'status', 'sentiment',
            'specific_description'
        )->get();

        // Analyze sentiments for complaints that need it
        $sentimentService = new SentimentAnalysisService();
        foreach ($complaints as $complaint) {
            if (!empty($complaint->specific_description) && empty($complaint->sentiment)) {
                $result = $sentimentService->analyzeSentiment($complaint->specific_description);

                // Update sentiment and determine appropriate status
                $complaint->sentiment = $result['sentiment'];

                // Only update status if it's pending or not set
                if (empty($complaint->status) || $complaint->status === 'pending') {
                    $complaint->status = match ($result['sentiment']) {
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

        $query = ComplaintRequest::select(
            'user_id', 'firstname', 'lastname', 'middle_name',
            'complaint_type', 'timestamp', 'status', 'sentiment',
            'specific_description'
        );

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $complaints = $query->orderBy('timestamp', 'desc')->get();

        // Analyze sentiments for complaints that need it
        $sentimentService = new SentimentAnalysisService();
        foreach ($complaints as $complaint) {
            if (!empty($complaint->specific_description)) {
                $result = $sentimentService->analyzeSentiment($complaint->specific_description);

                // Always update sentiment to get the latest analysis
                $complaint->sentiment = $result['sentiment'];

                // Only update status if it's pending or not set
                if (empty($complaint->status) || $complaint->status === 'pending') {
                    $complaint->status = match ($result['sentiment']) {
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
        $lastUpdate = ComplaintRequest::max('updated_at') ?: ComplaintRequest::max('timestamp');
        $complaintCount = ComplaintRequest::count();
        
        return response()->json([
            'last_update' => $lastUpdate,
            'complaint_count' => $complaintCount,
            'timestamp' => now()->toISOString()
        ]);
    }

    public function edit($user_id)
    {
        $complaint = ComplaintRequest::where('user_id', $user_id)->firstOrFail();            // Analyze sentiment if specific_description exists
            if (!empty($complaint->specific_description)) {
                try {
                    $sentimentService = new SentimentAnalysisService();
                    $result = $sentimentService->analyzeSentiment($complaint->specific_description);

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

                        // Only update status if it's pending or not set
                        if (empty($complaint->status) || $complaint->status === 'pending') {
                            // Use enhanced status determination
                            $complaint->status = match (true) {
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
                        'status' => $complaint->status
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
                'status' => 'required|in:pending,phase1,phase2,phase3,phase4,phase5,completed,rejected',
                'status_explanation' => 'nullable|string|max:500',
                'location' => 'nullable|string|max:100',
                'specific_description' => 'nullable|string|max:500'
            ]);

            // Analyze sentiment if description is updated
            $sentiment = $complaint->sentiment; // Keep existing sentiment by default
            if ($request->has('specific_description') && $request->specific_description) {
                $sentimentService = new SentimentAnalysisService();
                $result = $sentimentService->analyzeSentiment($request->specific_description);
                $sentiment = $result['sentiment'];
                Log::info("Sentiment analysis result for user_id {$user_id}", [
                    'sentiment' => $sentiment,
                    'scores' => $result['scores'] ?? null,
                    'matched_tokens' => $result['matched_tokens'] ?? null
                ]);
            }

            // Determine status based on sentiment
            $newStatus = match ($sentiment) {
                'negative' => 'phase3', // Set to investigation for negative sentiment
                'positive' => 'phase1', // Set to review for positive sentiment
                default => 'phase2', // Set to additional requirements for neutral
            };

            // Update the editable fields
            $update = [
                'status' => $newStatus,
                'status_explanation' => $request->status_explanation,
                'location' => $request->location,
                'specific_description' => $request->specific_description,
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
            $complaints = ComplaintRequest::whereNotNull('specific_description')
                ->where('specific_description', '!=', '')
                ->get();

            $updatedCount = 0;
            foreach ($complaints as $complaint) {
                $result = $sentimentService->analyzeSentiment($complaint->specific_description);

                // Update complaint with sentiment and determine status based on sentiment
                $newStatus = match ($result['sentiment']) {
                    'negative' => 'phase3', // Set to investigation for negative sentiment
                    'positive' => 'phase1', // Set to review for positive sentiment
                    default => 'phase2', // Set to additional requirements for neutral
                };

                $updated = ComplaintRequest::where('user_id', $result['user_id'])
                    ->update([
                        'sentiment' => $result['sentiment'],
                        'status' => $newStatus
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

