<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ResidentController extends Controller
{
    /**
     * Display a listing of the residents.
     */
    public function index()
    {
        $residents = Resident::orderBy('id', 'desc')->get();
        return view('residents', compact('residents'));
    }

    /**
     * Remove the specified resident from storage.
     */
    public function destroy($id)
    {
        $resident = Resident::findOrFail($id);
        $resident->delete();
        $this->triggerLiveUpdate();
        return redirect()->route('residents')->with('success', 'Resident deleted successfully.');
    }

    /**
     * Show the specified resident.
     */
    public function show($id)
    {
        $resident = Resident::findOrFail($id);
        return view('residents.show', compact('resident'));
    }

    /**
     * Toggle verification status of a resident
     */
    public function toggleVerification($id)
    {
        try {
            $resident = Resident::findOrFail($id);

            $resident->verified = !$resident->verified;
            $resident->save();

            $this->triggerLiveUpdate();

            $message = $resident->verified
                ? 'Resident has been verified successfully.'
                : 'Resident verification has been revoked.';

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'verified' => $resident->verified
                ]);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Error toggling resident verification:', [
                'resident_id' => $id,
                'error' => $e->getMessage()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating verification status. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error updating verification status. Please try again.');
        }
    }

    /**
     * Update the specified resident in storage.
     */
    public function update(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:residents,username,' . $resident->id,
            'email' => 'required|string|email|max:255|unique:residents,email,' . $resident->id,
            'contact_number' => 'nullable|string|max:255',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'validIDFront' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'validIDBack' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'middle_name' => $request->middle_name,
            'username' => $request->username,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
        ];

        if ($request->hasFile('profile')) {
            $updateData['profile'] = file_get_contents($request->file('profile')->getPathname());
        }

        if ($request->hasFile('validIDFront')) {
            $updateData['validIDFront'] = $request->file('validIDFront')->store('uploads/ids', 'public');
        }

        if ($request->hasFile('validIDBack')) {
            $updateData['validIDBack'] = $request->file('validIDBack')->store('uploads/ids', 'public');
        }

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $resident->update($updateData);

        $this->triggerLiveUpdate();

        $message = 'Resident updated successfully!';
        if ($request->hasFile('validIDFront') || $request->hasFile('validIDBack')) {
            $message = 'ID verification images updated successfully!';
        } elseif ($request->hasFile('profile')) {
            $message = 'Profile picture updated successfully!';
        }

        return redirect()->route('residents.view', $resident->id)->with('success', $message);
    }

    /**
     * Trigger live update notification
     */
    private function triggerLiveUpdate()
    {
        Cache::put('last_database_update', time(), 3600);
    }

    /**
     * API: Upload profile image for a resident
     */
    public function uploadProfileImage(Request $request, $id)
    {
        try {
            $resident = Resident::findOrFail($id);

            $request->validate([
                'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('profile')) {
                $resident->profile = file_get_contents($request->file('profile')->getPathname());
                $resident->save();

                $this->triggerLiveUpdate();

                return response()->json([
                    'success' => true,
                    'message' => 'Profile image uploaded successfully!',
                    'image_url' => route('resident.profile.image', $resident->id)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided'
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Error uploading profile image:', [
                'resident_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error uploading profile image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Upload ID images for a resident
     */
    public function uploadIDImages(Request $request, $id)
    {
        try {
            $resident = Resident::findOrFail($id);

            $request->validate([
                'validIDFront' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'validIDBack' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            $updateData = [];
            $uploadedFiles = [];

            if ($request->hasFile('validIDFront')) {
                $updateData['validIDFront'] = $request->file('validIDFront')->store('uploads/ids', 'public');
                $uploadedFiles[] = 'ID Front';
            }

            if ($request->hasFile('validIDBack')) {
                $updateData['validIDBack'] = $request->file('validIDBack')->store('uploads/ids', 'public');
                $uploadedFiles[] = 'ID Back';
            }

            if (!empty($updateData)) {
                $resident->update($updateData);
                $this->triggerLiveUpdate();

                $message = 'ID verification images updated successfully!';
                if (count($uploadedFiles) == 1) {
                    $message = $uploadedFiles[0] . ' image uploaded successfully!';
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'uploaded_files' => $uploadedFiles,
                    'id_front_url' => $resident->validIDFront ? asset('storage/' . $resident->validIDFront) : null,
                    'id_back_url' => $resident->validIDBack ? asset('storage/' . $resident->validIDBack) : null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No ID images provided'
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Error uploading ID images:', [
                'resident_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error uploading ID images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get profile image for a resident
     */
    public function getProfileImage($id)
    {
        try {
            $resident = Resident::findOrFail($id);

            if ($resident->profile) {
                return response()->json([
                    'success' => true,
                    'has_image' => true,
                    'image_url' => route('resident.profile.image', $resident->id),
                    'message' => 'Profile image found'
                ]);
            } else {
                $displayName = trim(($resident->firstname ?? '') . ' ' . ($resident->lastname ?? '')) ?: ($resident->username ?? $resident->email ?? 'User');

                return response()->json([
                    'success' => true,
                    'has_image' => false,
                    'fallback_url' => "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&color=7F9CF5&background=EBF4FF&size=150",
                    'message' => 'No profile image found, using fallback'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Resident not found'
            ], 404);
        }
    }

    /**
     * API: Get ID images for a resident
     */
    public function getIDImages($id)
    {
        try {
            $resident = Resident::findOrFail($id);

            return response()->json([
                'success' => true,
                'id_front' => [
                    'exists' => !empty($resident->validIDFront),
                    'url' => $resident->validIDFront ? asset('storage/' . $resident->validIDFront) : null
                ],
                'id_back' => [
                    'exists' => !empty($resident->validIDBack),
                    'url' => $resident->validIDBack ? asset('storage/' . $resident->validIDBack) : null
                ],
                'message' => 'ID images retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Resident not found'
            ], 404);
        }
    }
}
