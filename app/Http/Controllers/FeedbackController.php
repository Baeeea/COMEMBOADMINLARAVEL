<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeedbackController extends Controller
{    
    /**
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
     */    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'feedback' => 'required|string|max:1000',
        ]);

        Feedback::create([
            'user_id' => $request->user_id,
            'feedback' => $request->feedback,
        ]);

        return redirect()->route('feedback')->with('success', 'Feedback added successfully.');
    }    /**
     * Display the specified feedback.
     */
    public function show($id)
    {
        $feedback = Feedback::findOrFail($id);
        return view('feedback.show', compact('feedback'));
    }

    /**
     * Show the form for editing the specified feedback.
     */
    public function edit($id)
    {
        $feedback = Feedback::findOrFail($id);
        $users = User::all();
        return view('editfeedback', compact('feedback', 'users'));
    }

    /**
     * Update the specified feedback in storage.
     */    
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'feedback' => 'required|string|max:1000',
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'user_id' => $request->user_id,
            'feedback' => $request->feedback,
        ]);

        return redirect()->route('feedback')->with('success', 'Feedback updated successfully.');
    }

    /**
     * Remove the specified feedback from storage.
     */    
    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();
        return redirect()->route('feedback')->with('success', 'Feedback deleted successfully.');
    }
}
