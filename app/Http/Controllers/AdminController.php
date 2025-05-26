<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the admins.
     */
    public function index()
    {
        // Fetch admins from users table where role is 'admin' or 'super_admin'
        $admins = User::where('role', 'admin')
                     ->orWhere('role', 'super_admin')
                     ->select('name', 'role', 'email', 'id')
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('admin', compact('admins'));
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
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,super_admin',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
           
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        $this->triggerLiveUpdate();
        return redirect()->route('admin')->with('success', 'Admin created successfully!');
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
     */
    public function update(Request $request, $id)
    {
        $admin = User::where('id', $id)
                    ->where(function($query) {
                        $query->where('role', 'admin')
                              ->orWhere('role', 'super_admin');
                    })
                    ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
           
            'role' => 'required|string|in:admin,super_admin',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $admin->update($updateData);

        $this->triggerLiveUpdate();
        return redirect()->route('admin')->with('success', 'Admin updated successfully!');
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
                    ->firstOrFail();

        $admin->delete();

        $this->triggerLiveUpdate();
        return redirect()->route('admin')->with('success', 'Admin deleted successfully!');
    }

    /**
     * Trigger live update notification
     */
    private function triggerLiveUpdate()
    {
        Cache::put('last_database_update', time(), 3600);
    }
}
