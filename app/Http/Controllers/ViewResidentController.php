<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ViewResidentController extends Controller
{    /**
     * Display detailed view of a specific resident.
     */
    public function show($id)
    {        // Use the correct primary key field (user_id instead of id)
        // The $id parameter from the route corresponds to user_id in the database
        $resident = Resident::where('user_id', $id)->firstOrFail();// Debug: Log resident data
        Log::info('ViewResidentController - Resident data:', [
            'id_parameter' => $id,
            'user_id' => $resident->id,
            'firstname' => $resident->firstname,
            'lastname' => $resident->lastname,
            'email' => $resident->email,
            'full_data' => $resident->toArray(),
        ]);

        return view('viewresidents', compact('resident'));
    }
}
