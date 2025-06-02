<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    // Explicitly specify the table name
    protected $table = 'documentrequest';

    // If your timestamps column is named differently than created_at/updated_at
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = null; // Set to null if you don't have an updated_at column

    // Make all fields fillable
    protected $fillable = [
        'id',
        'firstname',
        'lastname',
        'middle_name',
        'birthdate',
        'age',
        'contact_number',
        'home_address',
        'user_id',
        'years_residency',
        'civil_status',
        'business_name',
        'business_type',
        'business_owner',
        'local_employment',
        'business_address',
        'purpose',
        'document_type',
        'project_description',
        'status',
        'status_explanation',
        'timestamp',
        'validIDFront',
        'validIDBack',
        'image',
        'image2',
        'image3',
        'photo_store',
        'photo_current_house', 
        'photo_renovation',
        'photo_proof',
        'child_name'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'birthdate' => 'date',
        'age' => 'integer',
        'contact_number' => 'integer',
        'years_residency' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'photo_store',
        'photo_current_house',
        'photo_renovation',
        'photo_proof',
        'validIDFront',
        'validIDBack',
        'image',
        'image2',
        'image3'
    ];

    /**
     * Get the resident associated with this document request.
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class, 'user_id', 'user_id');
    }
}
