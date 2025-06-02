<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ViewResidentController extends Controller
{    /**
     * Display detailed view of a specific resident.
     */
    public function show($id)
    {
        // Find resident by user_id
        $resident = Resident::where('user_id', $id)->firstOrFail();        // Debug: Log resident data
        Log::info('ViewResidentController - Resident data:', [
            'id_parameter' => $id,
            'resident_id' => $resident->user_id,
            'firstname' => $resident->firstname,
            'lastname' => $resident->lastname,
            'email' => $resident->email,
            'full_data' => $resident->toArray(),
        ]);

        return view('viewresidents', compact('resident'));
    }

    /**
     * RESTful API endpoint to upload ID images as BLOB data to MySQL
     */
    public function uploadIDImages(Request $request, $id)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'validIDFront' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
                'validIDBack' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',  // 5MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }            // Find the resident
            $resident = Resident::where('user_id', $id)->firstOrFail();

            $updatedFields = [];

            // Process ID Front image
            if ($request->hasFile('validIDFront')) {
                $idFrontFile = $request->file('validIDFront');
                
                // Convert image to BLOB data
                $idFrontData = file_get_contents($idFrontFile->getPathname());
                $resident->id_front = $idFrontData;
                $updatedFields[] = 'ID Front';
                
                Log::info('ID Front image uploaded', [
                    'resident_id' => $id,
                    'file_size' => strlen($idFrontData),
                    'mime_type' => $idFrontFile->getMimeType()
                ]);
            }

            // Process ID Back image
            if ($request->hasFile('validIDBack')) {
                $idBackFile = $request->file('validIDBack');
                
                // Convert image to BLOB data
                $idBackData = file_get_contents($idBackFile->getPathname());
                $resident->id_back = $idBackData;
                $updatedFields[] = 'ID Back';
                
                Log::info('ID Back image uploaded', [
                    'resident_id' => $id,
                    'file_size' => strlen($idBackData),
                    'mime_type' => $idBackFile->getMimeType()
                ]);            }            // Save the resident with BLOB data
            $resident->save();            // Image upload completed

            return response()->json([
                'success' => true,
                'message' => 'ID images uploaded successfully to database as BLOB data',
                'updated_fields' => $updatedFields,
                'data' => [
                    'id_front_exists' => !empty($resident->id_front),
                    'id_back_exists' => !empty($resident->id_back),
                    'id_front_size' => !empty($resident->id_front) ? strlen($resident->id_front) : 0,
                    'id_back_size' => !empty($resident->id_back) ? strlen($resident->id_back) : 0,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error uploading ID images via API', [
                'resident_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload ID images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * RESTful API endpoint to serve ID images from BLOB data
     */    public function serveIDImage($id, $type)
    {
        try {
            $resident = Resident::where('user_id', $id)->firstOrFail();
            
            $imageData = null;
            $contentType = 'image/jpeg'; // Default content type

            if ($type === 'front' && $resident->id_front) {
                $imageData = $resident->id_front;
            } elseif ($type === 'back' && $resident->id_back) {
                $imageData = $resident->id_back;
            }

            if (!$imageData) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID image not found'
                ], 404);
            }

            // Detect content type from image data
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $detectedType = $finfo->buffer($imageData);
            if ($detectedType && strpos($detectedType, 'image/') === 0) {
                $contentType = $detectedType;
            }

            return response($imageData)
                ->header('Content-Type', $contentType)
                ->header('Content-Length', strlen($imageData))
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Last-Modified', gmdate('D, d M Y H:i:s', strtotime($resident->updated_at)) . ' GMT');

        } catch (\Exception $e) {
            Log::error('Error serving ID image', [
                'resident_id' => $id,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to serve ID image'
            ], 500);
        }
    }

    /**
     * RESTful API endpoint to get ID images metadata
     */    public function getIDImages($id)
    {
        try {
            $resident = Resident::where('user_id', $id)->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'id_front' => [
                        'exists' => !empty($resident->id_front),
                        'url' => !empty($resident->id_front) ? route('residents.id.image', ['id' => $resident->user_id, 'type' => 'front']) : null,
                        'size' => !empty($resident->id_front) ? strlen($resident->id_front) : 0
                    ],
                    'id_back' => [
                        'exists' => !empty($resident->id_back),
                        'url' => !empty($resident->id_back) ? route('residents.id.image', ['id' => $resident->user_id, 'type' => 'back']) : null,
                        'size' => !empty($resident->id_back) ? strlen($resident->id_back) : 0
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting ID images metadata', [
                'resident_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get ID images metadata'
            ], 500);
        }
    }

    /**
     * RESTful API endpoint to delete ID images
     */    public function deleteIDImage(Request $request, $id)
    {
        try {
            $type = $request->input('type'); // 'front', 'back', or 'both'
            
            if (!in_array($type, ['front', 'back', 'both'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image type. Use: front, back, or both'
                ], 422);
            }

            $resident = Resident::where('user_id', $id)->firstOrFail();
            $deletedFields = [];

            if ($type === 'front' || $type === 'both') {
                $resident->id_front = null;
                $deletedFields[] = 'ID Front';
            }

            if ($type === 'back' || $type === 'both') {
                $resident->id_back = null;
                $deletedFields[] = 'ID Back';
            }

            $resident->save();

            return response()->json([
                'success' => true,
                'message' => 'ID images deleted successfully',
                'deleted_fields' => $deletedFields
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting ID images', [
                'resident_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ID images'
            ], 500);
        }
    }

    /**
     * RESTful API endpoint to update ID images (PUT method)
     */
    public function updateIDImages(Request $request, $id)
    {
        // This method uses the same logic as uploadIDImages but for PUT requests
        return $this->uploadIDImages($request, $id);
    }    /**
     * Toggle verification status of a resident
     */    public function toggleVerification(Request $request, $id)
    {
        try {
            // Log the incoming parameters for debugging
            Log::info('toggleVerification called', [
                'id_parameter' => $id,
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            // Validate that ID is not null
            if (empty($id)) {
                throw new \Exception('Resident ID is required and cannot be empty');
            }

            $resident = Resident::where('user_id', $id)->firstOrFail();
            
            // Toggle status between "Not Verified" and "Verified"
            if ($resident->status === 'Not Verified') {
                $resident->status = 'Verified';
                $message = 'Resident has been verified successfully';
            } else {
                $resident->status = 'Not Verified';
                $message = 'Resident verification has been revoked';
            }
              $resident->save();Log::info('Resident verification status updated', [
                'resident_id' => $id,
                'new_status' => $resident->status,
                'updated_by' => auth()->id()
            ]);

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'status' => $resident->status
                ]);
            }

            // Redirect for regular form submissions
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error updating verification status', [
                'resident_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating verification status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error updating verification status. Please try again.');        }
    }
}
