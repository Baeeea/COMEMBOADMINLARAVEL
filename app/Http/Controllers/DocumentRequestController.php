<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request; // Add this import statement
use Illuminate\Support\Facades\Cache;

class DocumentRequestController extends Controller
{
    private function triggerLiveUpdate()
    {
        Cache::put('last_database_update', time(), 3600);
    }
    public function index()
    {
        $totalRequests = DocumentRequest::count();
        $requests = DocumentRequest::select('firstname', 'lastname', 'middle_name', 'document_type', 'timestamp', 'status')->get();
        return view('documentrequest', compact('totalRequests', 'requests'));
    }
    public function fetchData(Request $request)
{
    $status = $request->query('status');

    // Now that we've added the id column, we can explicitly select it
    $query = DocumentRequest::select('id', 'firstname', 'lastname', 'middle_name', 'document_type', 'timestamp', 'status');

    if ($status && $status !== 'all') {
        $query->where('status', $status);
    }

    $requests = $query->get();

    return response()->json($requests);
}
public function store(Request $request)
{
    // Access request data
    $data = $request->all();
    // ...
}

public function edit($id)
{
    // Find the document request by ID - now using the new id column
    $document = DocumentRequest::findOrFail($id);

    // Return the edit view with the document data
    return view('editdocument', compact('document'));
}

public function update(Request $request, $id)
{
    // Find the document request
    $document = DocumentRequest::findOrFail($id);

    // Update basic information
    $document->firstname = $request->firstname;
    $document->lastname = $request->lastname;
    $document->middle_name = $request->middle_name;

    // Handle birthdate - ensure it's not null (use current date as default if empty)
    $document->birthdate = $request->birthdate ?: date('Y-m-d');

    // Handle age - set to 0 if null
    $document->age = $request->age ?: 0;

    // Handle contact number to accept up to 10 digits
    if ($request->contact_number) {
        // Clean the phone number to contain only digits
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->contact_number);

        // Make sure we only store up to 10 digits to prevent integer overflow
        // Most Philippine mobile numbers are 10 digits (without the leading zero)
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);  // Keep only the last 10 digits
        }

        // Try to save as integer, but if too large, save as largest possible integer
        try {
            $document->contact_number = (int)$cleanPhone;
        } catch (\Exception $e) {
            // If conversion fails, use PHP_INT_MAX (maximum integer value)
            $document->contact_number = PHP_INT_MAX;
        }
    }

    // Handle other fields with possible NOT NULL constraints
    $document->home_address = $request->home_address ?: '';
    $document->years_residency = $request->years_residency ?: '0';
    $document->civil_status = $request->civil_status ?: 'Single';
    $document->local_employment = $request->local_employment ?: '';

    // Business information - use empty strings instead of null
    $document->business_name = $request->business_name ?: '';
    $document->business_type = $request->business_type ?: '';
    $document->business_owner = $request->business_owner ?: '';
    $document->business_address = $request->business_address ?: '';

    // Document information
    $document->document_type = $request->document_type;
    $document->purpose = $request->purpose;
    $document->project_description = $request->project_description;
    $document->status = $request->status;
    $document->status_explanation = $request->status_explanation;

    // Handle image uploads
    if ($request->hasFile('validIDFront')) {
        $validIDFront = $request->file('validIDFront');
        $filename = 'validID_front_' . time() . '.' . $validIDFront->getClientOriginalExtension();
        $validIDFront->move(public_path('uploads/documents'), $filename);
        $document->validIDFront = 'uploads/documents/' . $filename;
    }

    if ($request->hasFile('validIDBack')) {
        $validIDBack = $request->file('validIDBack');
        $filename = 'validID_back_' . time() . '.' . $validIDBack->getClientOriginalExtension();
        $validIDBack->move(public_path('uploads/documents'), $filename);
        $document->validIDBack = 'uploads/documents/' . $filename;
    }

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $filename = 'image_' . time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/documents'), $filename);
        $document->image = 'uploads/documents/' . $filename;
    }

    if ($request->hasFile('image2')) {
        $image2 = $request->file('image2');
        $filename = 'image2_' . time() . '.' . $image2->getClientOriginalExtension();
        $image2->move(public_path('uploads/documents'), $filename);
        $document->image2 = 'uploads/documents/' . $filename;
    }

    if ($request->hasFile('image3')) {
        $image3 = $request->file('image3');
        $filename = 'image3_' . time() . '.' . $image3->getClientOriginalExtension();
        $image3->move(public_path('uploads/documents'), $filename);
        $document->image3 = 'uploads/documents/' . $filename;
    }

    // Save the changes
    $document->save();

    $this->triggerLiveUpdate();
    // Redirect back to the document requests page with a success message
    return redirect()->route('documentrequest')->with('success', 'Document request updated successfully');
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

    // Delete any uploaded files if they exist
    $fileFields = ['validIDFront', 'validIDBack', 'image', 'image2', 'image3'];

    foreach ($fileFields as $field) {
        if (!empty($document->$field) && file_exists(public_path($document->$field))) {
            unlink(public_path($document->$field));
        }
    }

    // Delete the document request
    $document->delete();

    $this->triggerLiveUpdate();
    // Redirect back to the document requests page with a success message
    return redirect()->route('documentrequest')->with('success', 'Document request deleted successfully');
}
}

