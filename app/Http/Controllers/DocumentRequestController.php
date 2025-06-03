<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DocumentRequestController extends Controller
{
    public function index()
    {
        $totalRequests = DocumentRequest::count();
        
        // Join with residents table to get resident names and id images using user_id, rest from documentrequest
        $requests = DocumentRequest::leftJoin('residents', 'documentrequest.user_id', '=', 'residents.user_id')
            ->select(
                'documentrequest.id', 
                'documentrequest.document_type', 
                'documentrequest.created_at', 
                'documentrequest.status',
                'documentrequest.user_id',
                // Get names and id images from residents table only
                'residents.first_name as firstname',
                'residents.last_name as lastname',
                'residents.id_front',
                'residents.id_back'
            )
            ->get();
            
        return view('documentrequest', compact('totalRequests', 'requests'));
    }

    public function fetchData(Request $request)
    {
        try {
            $status = $request->query('status');

            // Join with residents table to get resident names and id images using user_id, rest from documentrequest
            $query = DocumentRequest::leftJoin('residents', 'documentrequest.user_id', '=', 'residents.user_id')
                ->select(
                    'documentrequest.id', 
                    'documentrequest.document_type', 
                    DB::raw('CONVERT(documentrequest.created_at USING utf8) as timestamp'),
                    DB::raw('CONVERT(documentrequest.status USING utf8) as status'),
                    'documentrequest.user_id',
                    DB::raw('CONVERT(residents.first_name USING utf8) as firstname'),
                    DB::raw('CONVERT(residents.last_name USING utf8) as lastname')
                );

            // Enhanced status filtering with normalization
            if ($status && $status !== 'all') {
                $normalizedStatus = mb_strtolower(trim($status));
                switch ($normalizedStatus) {
                    case 'in process':
                    case 'in-process':
                    case 'inprocess':
                        $query->whereRaw('LOWER(TRIM(documentrequest.status)) IN (?, ?, ?)', 
                            ['in process', 'in-process', 'inprocess']);
                        break;
                    default:
                        $query->whereRaw('LOWER(TRIM(documentrequest.status)) = ?', [$normalizedStatus]);
                        break;
                }
            }

            $requests = $query->orderBy('documentrequest.created_at', 'desc')->get();
            
            // Ensure proper encoding for all string values
            $requests = $requests->map(function ($item) {
                $item->firstname = mb_convert_encoding($item->firstname ?? '', 'UTF-8', 'UTF-8');
                $item->lastname = mb_convert_encoding($item->lastname ?? '', 'UTF-8', 'UTF-8');
                $item->document_type = mb_convert_encoding($item->document_type ?? '', 'UTF-8', 'UTF-8');
                $item->status = mb_convert_encoding($item->status ?? '', 'UTF-8', 'UTF-8');
                return $item;
            });

            return response()->json($requests, 200, [
                'Content-Type' => 'application/json;charset=UTF-8'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in fetchData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(
                ['error' => 'Failed to fetch data: ' . $e->getMessage()],
                500,
                ['Content-Type' => 'application/json;charset=UTF-8']
            );
        }
    }

    public function store(Request $request)
    {
        // Access request data
        $data = $request->all();
        // ...
    }

    public function edit($id)
    {
        // Find the document request by ID with resident relationship
        $document = DocumentRequest::with('resident')->findOrFail($id);
        
        // Create a data object that merges document request data with resident data
        // Prioritize resident table data over document request data for user information
        $data = new \stdClass();
        
        // Document-specific fields (always from document request)
        $data->id = $document->id;
        $data->user_id = $document->user_id; // Add user_id
        $data->document_type = $document->document_type;
        $data->purpose = $document->purpose;
        $data->status = $document->status;
        $data->status_explanation = $document->status_explanation;
        $data->project_description = $document->project_description;
        $data->validIDFront = $document->validIDFront;
        $data->validIDBack = $document->validIDBack;
        $data->image = $document->image;
        $data->image2 = $document->image2;
        $data->image3 = $document->image3;
        $data->created_at = $document->created_at;
        $data->updated_at = $document->updated_at;
        
        // Business-related fields (from document request)
        $data->business_name = $document->business_name;
        $data->business_type = $document->business_type;
        $data->business_owner = $document->business_owner;
        $data->business_address = $document->business_address;
        $data->local_employment = $document->local_employment;
        
        // Additional photo fields for specific document types
        $data->photo_store = $document->photo_store ?? '';
        $data->photo_current_house = $document->photo_current_house ?? '';
        $data->photo_renovation = $document->photo_renovation ?? '';
        $data->photo_proof = $document->photo_proof ?? '';
        $data->child_name = $document->child_name ?? '';
        
        // User information: prioritize resident data if available, fallback to document data
        if ($document->resident) {
            $resident = $document->resident;
            $data->firstname = $resident->first_name ?? $document->firstname;
            $data->lastname = $resident->last_name ?? $document->lastname;
            $data->middle_name = $resident->middle_name ?? $document->middle_name;
            $data->birthdate = $resident->birthdate ?? $document->birthdate;
            $data->age = $resident->age ?? $document->age;
            $data->home_address = $resident->home_address ?? $document->home_address;
            $data->contact_number = $resident->contact_number ?? $document->contact_number;
            $data->years_residency = $resident->years_residency ?? $document->years_residency;
            $data->civil_status = $resident->civil_status ?? $document->civil_status;
            $data->id_front = $resident->id_front ?? null;
            $data->id_back = $resident->id_back ?? null;
        } else {
            $data->firstname = $document->firstname;
            $data->lastname = $document->lastname;
            $data->middle_name = $document->middle_name;
            $data->birthdate = $document->birthdate;
            $data->age = $document->age;
            $data->home_address = $document->home_address;
            $data->contact_number = $document->contact_number;
            $data->years_residency = $document->years_residency;
            $data->civil_status = $document->civil_status;
            $data->id_front = null;
            $data->id_back = null;
        }

        // Return the edit view with the merged data
        return view('editdocument', ['document' => $data]);
    }

    public function update(Request $request, $id)
    {
        try {
            \Log::info('Document update started', ['id' => $id]);
            
            // Find the document request
            $document = DocumentRequest::findOrFail($id);

            // Only validate the fields that are actually being submitted in the form
            $validatedData = $request->validate([
                'status' => 'required|string|in:pending,inprocess,completed,rejected',
                'status_explanation' => 'nullable|string'
            ]);

            // Update only the status field (which is the only editable field in the form)
            $document->status = $request->status;
            if ($request->has('status_explanation')) {
                $document->status_explanation = $request->status_explanation;
            }

            // Save the changes
            if ($document->save()) {
                \Log::info('Document saved successfully', ['id' => $id]);
                
                return redirect()->route('documentrequest')
                               ->with('success', 'Document request status updated successfully');
            } else {
                throw new \Exception('Failed to save document request');
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Document request not found:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('documentrequest')
                           ->with('error', 'Document request not found');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error updating document request:', [
                'id' => $id,
                'errors' => $e->errors()
            ]);

            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput();

        } catch (\Exception $e) {
            \Log::error('Error updating document request:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                           ->with('error', 'Failed to update document request: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified document request from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Find the document request by ID
        $document = DocumentRequest::findOrFail($id);

        // No need to delete physical files since we're using BLOB data
        // Just delete the document request and all its BLOB data will be removed from the database
        $document->delete();

        // Redirect back to the document requests page with a success message
        return redirect()->route('documentrequest')->with('success', 'Document request deleted successfully');
    }

    // Debug method to test data fetching
    public function debug()
    {
        try {
            // Test basic query without joins
            $basicData = DocumentRequest::select('id', 'document_type', 'created_at', 'status', 'user_id')->limit(5)->get();
            
            // Test with join
            $joinData = DocumentRequest::leftJoin('residents', 'documentrequest.user_id', '=', 'residents.user_id')
                ->select(
                    'documentrequest.id', 
                    'documentrequest.document_type', 
                    'documentrequest.created_at as timestamp', 
                    'documentrequest.status',
                    'documentrequest.user_id',
                    'residents.first_name as firstname',
                    'residents.last_name as lastname',
                    'residents.id_front',
                    'residents.id_back'
                )
                ->limit(5)
                ->get();
            
            return response()->json([
                'basic_data' => $basicData,
                'join_data' => $joinData,
                'table_count' => DocumentRequest::count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get photo_store from database as BLOB data
     */
    public function getPhotoStore($id)
    {
        $document = DocumentRequest::findOrFail($id);
        
        if (!$document->photo_store) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        // Detect MIME type from binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($document->photo_store);
        
        return response($document->photo_store)
            ->header('Content-Type', $mimeType ?: 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get photo_current_house from database as BLOB data
     */
    public function getPhotoCurrentHouse($id)
    {
        $document = DocumentRequest::findOrFail($id);
        
        if (!$document->photo_current_house) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        // Detect MIME type from binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($document->photo_current_house);
        
        return response($document->photo_current_house)
            ->header('Content-Type', $mimeType ?: 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get photo_renovation from database as BLOB data
     */
    public function getPhotoRenovation($id)
    {
        $document = DocumentRequest::findOrFail($id);
        
        if (!$document->photo_renovation) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        // Detect MIME type from binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($document->photo_renovation);
        
        return response($document->photo_renovation)
            ->header('Content-Type', $mimeType ?: 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get photo_proof from database as BLOB data
     */
    public function getPhotoProof($id)
    {
        $document = DocumentRequest::findOrFail($id);
        
        if (!$document->photo_proof) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        // Detect MIME type from binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($document->photo_proof);
        
        return response($document->photo_proof)
            ->header('Content-Type', $mimeType ?: 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get validIDFront from database as BLOB data
     */
    public function getValidIDFront($id)
    {
        $document = DocumentRequest::findOrFail($id);
        
        if (!$document->validIDFront) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        // Detect MIME type from binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($document->validIDFront);
        
        return response($document->validIDFront)
            ->header('Content-Type', $mimeType ?: 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get validIDBack from database as BLOB data
     */
    public function getValidIDBack($id)
    {
        $document = DocumentRequest::findOrFail($id);
        
        if (!$document->validIDBack) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        // Detect MIME type from binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($document->validIDBack);
        
        return response($document->validIDBack)
            ->header('Content-Type', $mimeType ?: 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get image from database as BLOB data
     */
    public function getImage($id)
    {
        $document = DocumentRequest::findOrFail($id);
        
        if (!$document->image) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        // Detect MIME type from binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($document->image);
        
        return response($document->image)
            ->header('Content-Type', $mimeType ?: 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get image2 from database as BLOB data
     */
    public function getImage2($id)
    {
        $document = DocumentRequest::findOrFail($id);
        
        if (!$document->image2) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        // Detect MIME type from binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($document->image2);
        
        return response($document->image2)
            ->header('Content-Type', $mimeType ?: 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get image3 from database as BLOB data
     */
    public function getImage3($id)
    {
        $document = DocumentRequest::findOrFail($id);
        
        if (!$document->image3) {
            return response()->json(['error' => 'Photo not found'], 404);
        }

        // Detect MIME type from binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($document->image3);
        
        return response($document->image3)
            ->header('Content-Type', $mimeType ?: 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get ID front image from residents table as BLOB data
     * 
     * @param int $user_id User ID
     * @return \Illuminate\Http\Response
     */
    public function getIdFront($user_id)
    {
        try {
            $resident = \App\Models\Resident::where('user_id', $user_id)->first();
            
            if (!$resident || !$resident->id_front) {
                return response()->json(['error' => 'ID Front photo not found'], 404);
            }

            // Detect MIME type from binary data
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($resident->id_front);
            
            // Generate ETag for client-side caching
            $lastModified = $resident->updated_at ? $resident->updated_at->timestamp : time();
            $etag = md5($resident->id_front . $lastModified);
            
            // Return binary image data with proper headers
            return response($resident->id_front)
                ->header('Content-Type', $mimeType ?: 'image/jpeg')
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('ETag', '"' . $etag . '"');
        } catch (\Exception $e) {
            \Log::error('Error serving ID Front image:', [
                'user_id' => $user_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to retrieve ID Front image',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ID back image from residents table as BLOB data
     * 
     * @param int $user_id User ID
     * @return \Illuminate\Http\Response
     */
    public function getIdBack($user_id)
    {
        try {
            $resident = \App\Models\Resident::where('user_id', $user_id)->first();
            
            if (!$resident || !$resident->id_back) {
                return response()->json(['error' => 'ID Back photo not found'], 404);
            }

            // Detect MIME type from binary data
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($resident->id_back);
            
            // Generate ETag for client-side caching
            $lastModified = $resident->updated_at ? $resident->updated_at->timestamp : time();
            $etag = md5($resident->id_back . $lastModified);
            
            // Return binary image data with proper headers
            return response($resident->id_back)
                ->header('Content-Type', $mimeType ?: 'image/jpeg')
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('ETag', '"' . $etag . '"');
        } catch (\Exception $e) {
            \Log::error('Error serving ID Back image:', [
                'user_id' => $user_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to retrieve ID Back image',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get image metadata (formats, sizes, mime types)
     * 
     * @param int $id Document request ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImageMetadata($id)
    {
        try {
            $document = DocumentRequest::with('resident')->findOrFail($id);
            $metadata = [];
            
            // Helper function to get image information
            $getImageInfo = function($imageData) {
                if (!$imageData) return null;
                
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($imageData);
                $size = strlen($imageData);
                
                return [
                    'size_bytes' => $size,
                    'size_formatted' => $this->formatBytes($size),
                    'mime_type' => $mimeType
                ];
            };
            
            // Gather metadata for all available images
            if ($document->resident) {
                $metadata['id_front'] = [
                    'exists' => !empty($document->resident->id_front),
                    'info' => $getImageInfo($document->resident->id_front),
                    'url' => !empty($document->resident->id_front) ? 
                        route('api.documentrequest.idFront', $document->user_id) : null
                ];
                
                $metadata['id_back'] = [
                    'exists' => !empty($document->resident->id_back),
                    'info' => $getImageInfo($document->resident->id_back),
                    'url' => !empty($document->resident->id_back) ? 
                        route('api.documentrequest.idBack', $document->user_id) : null
                ];
            }
            
            // Document request images
            $metadata['valid_id_front'] = [
                'exists' => !empty($document->validIDFront),
                'info' => $getImageInfo($document->validIDFront),
                'url' => !empty($document->validIDFront) ? 
                    route('api.documentrequest.validIDFront', $document->id) : null
            ];
            
            $metadata['valid_id_back'] = [
                'exists' => !empty($document->validIDBack),
                'info' => $getImageInfo($document->validIDBack),
                'url' => !empty($document->validIDBack) ? 
                    route('api.documentrequest.validIDBack', $document->id) : null
            ];
            
            // Return the metadata as JSON
            return response()->json([
                'success' => true,
                'document_id' => $id, 
                'images' => $metadata
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting image metadata:', [
                'document_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve image metadata',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Helper function to format bytes to human-readable format
     * 
     * @param int $bytes Number of bytes
     * @param int $precision Decimal precision
     * @return string Formatted size string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}