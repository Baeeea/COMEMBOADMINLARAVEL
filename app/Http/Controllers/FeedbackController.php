<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeedbackController extends Controller
{    /**
     * Display a listing of the feedback.
     */
    public function index()
    {
        $feedbacks = Feedback::with('user')->orderBy('created_at', 'desc')->get();
        return view('feedback', compact('feedbacks'));
    }

    /**
     * Show the form for creating a new feedback.
     */
    public function create()
    {
        $users = User::all();
        return view('editfeedback', compact('users'));
    }

    /**
     * Store a newly created feedback in storage.
     */    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'feedback' => 'required|string|max:1000',
        ]);

        Feedback::create([
            'user_id' => $request->user_id,
            'feedback' => $request->feedback,
        ]);

        $this->triggerLiveUpdate();

        return redirect()->route('feedback')->with('success', 'Feedback added successfully.');
    }

    /**
     * Display the specified feedback.
     */
    public function show(Feedback $feedback)
    {
        return view('feedback.show', compact('feedback'));
    }

    /**
     * Show the form for editing the specified feedback.
     */
    public function edit(Feedback $feedback)
    {
        $users = User::all();
        return view('editfeedback', compact('feedback', 'users'));
    }

    /**
     * Update the specified feedback in storage.
     */    public function update(Request $request, Feedback $feedback)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'feedback' => 'required|string|max:1000',
        ]);

        $feedback->update([
            'user_id' => $request->user_id,
            'feedback' => $request->feedback,
        ]);

        $this->triggerLiveUpdate();

        return redirect()->route('feedback')->with('success', 'Feedback updated successfully.');
    }

    /**
     * Remove the specified feedback from storage.
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        $this->triggerLiveUpdate();
        return redirect()->route('feedback')->with('success', 'Feedback deleted successfully.');
    }

    /**
     * Trigger live update notification
     */
    private function triggerLiveUpdate()
    {
        Cache::put('last_database_update', time(), 3600);
    }
}
