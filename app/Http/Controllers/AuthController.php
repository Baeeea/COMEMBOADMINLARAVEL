<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate email and password inputs
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check plain-text password (NOT secure, for testing only)
        if ($user && $user->password === $request->password) {
            // Log the user in manually
            Auth::login($user);

            // Regenerate session to prevent fixation
            $request->session()->regenerate();

            // Redirect to dashboard
            return redirect()->route('dashboard');
        }

        // Login failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Invalidate the session and regenerate CSRF token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login.form')->with('message', 'You have been logged out successfully.');
    }
    public function showLoginForm()
{
    return view('login');  // loads resources/views/login.blade.php
}

}
