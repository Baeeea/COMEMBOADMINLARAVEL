<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ResidentController extends Controller
    {        /**
         * Display a listing of the residents.
         */        public function index()
        {
            $residents = Resident::orderBy('created_at', 'desc')->get();
            return view('residents', compact('residents'));
        }/**
        * Remove the specified resident from storage.
        */        public function destroy($id)
        {
            $resident = Resident::findOrFail($id);
            $resident->delete();
            $this->triggerLiveUpdate();
            return redirect()->route('residents')->with('success', 'Resident deleted successfully.');
        }

        /**
         * Show the specified resident.
         */        public function show($id)
        {
            $resident = Resident::findOrFail($id);
            return view('residents.show', compact('resident'));        }

        /**
         * Toggle verification status of a resident
         */
        public function toggleVerification($id)
        {
            try {
                $resident = Resident::where('user_id', $id)->firstOrFail();
                
                // Toggle the verification status
                $resident->verified = !$resident->verified;
                $resident->save();
                
                $this->triggerLiveUpdate();
                
                $message = $resident->verified 
                    ? 'Resident has been verified successfully.' 
                    : 'Resident verification has been revoked.';
                
                // Check if request is AJAX
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
         * Trigger live update notification
         */
        private function triggerLiveUpdate()
        {
            Cache::put('last_database_update', time(), 3600);
        }
    }
