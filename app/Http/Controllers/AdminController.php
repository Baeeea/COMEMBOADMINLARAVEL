<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{    /**
     * Display a listing of all users.
     */    public function index()
    {
        // Fetch ALL users from users table
        $users = User::select('name', 'role', 'email', 'id', 'created_at', 'email_verified_at')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('admin', compact('users'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        return view('admin.create');
    }

    /**
     * Store a newly created admin in storage.
     */    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:user,admin,super_admin',
        ]);        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,        ]);

        return redirect()->route('admin.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified admin.
     */
    public function show($id)
    {
        $admin = User::where('id', $id)
                    ->where(function($query) {
                        $query->where('role', 'admin')
                              ->orWhere('role', 'super_admin');
                    })
                    ->firstOrFail();

        return view('viewadmin', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit($id)
    {
        $admin = User::where('id', $id)
                    ->where(function($query) {
                        $query->where('role', 'admin')
                              ->orWhere('role', 'super_admin');
                    })
                    ->firstOrFail();

        return view('admin.edit', compact('admin'));
    }

    /**
     * Update the specified admin in storage.
     */    public function update(Request $request, $id)
    {
        $admin = User::where('id', $id)
                    ->where(function($query) {
                        $query->where('role', 'admin')
                              ->orWhere('role', 'super_admin');
                    })
                    ->firstOrFail();        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'role' => 'required|string|in:admin,super_admin',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Handle optional fields
        if ($request->has('firstname')) {
            $updateData['firstname'] = $request->firstname;
        }
        
        if ($request->has('lastname')) {
            $updateData['lastname'] = $request->lastname;
        }        // Handle profile picture upload for BLOB storage
        if ($request->hasFile('profile')) {
            $profilePhoto = $request->file('profile');
            
            // Read the file content as binary data
            $imageData = file_get_contents($profilePhoto->getPathname());
            
            // Store binary data directly in the database
            $updateData['profile'] = $imageData;
        }

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }        $admin->update($updateData);

        return redirect()->route('admin.show', $admin->id)->with('success', 'Profile updated successfully!');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy($id)
    {
        $admin = User::where('id', $id)
                    ->where(function($query) {
                        $query->where('role', 'admin')
                              ->orWhere('role', 'super_admin');
                    })
                    ->firstOrFail();        $admin->delete();

        return redirect()->route('admin')->with('success', 'Admin deleted successfully!');    }

    /**
     * Upload profile image for the current authenticated user
     */
    public function uploadProfile(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        
        if ($request->hasFile('profile_image')) {
            $profilePhoto = $request->file('profile_image');
            
            // Read the file content as binary data
            $imageData = file_get_contents($profilePhoto->getPathname());            // Store binary data directly in the database
            $user->update(['profile' => $imageData]);
            
            return redirect()->back()->with('success', 'Profile image updated successfully!');
        }

        return redirect()->back()->with('error', 'Failed to upload profile image.');
    }    /**
     * Update profile information for the current authenticated user
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Handle optional fields
        if ($request->has('firstname')) {
            $updateData['firstname'] = $request->firstname;
        }
        
        if ($request->has('lastname')) {
            $updateData['lastname'] = $request->lastname;
        }

        // Handle profile picture upload for BLOB storage
        if ($request->hasFile('profile')) {
            $profilePhoto = $request->file('profile');
            
            // Read the file content as binary data
            $imageData = file_get_contents($profilePhoto->getPathname());
            
            // Store binary data directly in the database
            $updateData['profile'] = $imageData;
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }/**
     * Show the current authenticated user's profile
     */
    public function profile()
    {
        // Get the current user's ID
        $userId = auth()->id();
        
        // Fetch the complete user data from the users table
        $admin = User::where('id', $userId)->firstOrFail();

        return view('profile', compact('admin'));
    }

    /**
     * Update password for the current authenticated user
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if current password is correct
        if (!password_verify($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
}
