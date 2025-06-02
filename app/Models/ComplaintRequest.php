<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintRequest extends Model
{
    use HasFactory;

    // Explicit table name
    protected $table = 'complaintrequests';

    // Set user_id as primary key
    protected $primaryKey = 'user_id';

    // Fillable fields for mass assignment
    protected $fillable = [
        'user_id',
        'birthdate',
        'age',
        'contact_number',
        'home_address',
        'complaint_type',
        'disturbance_type',
        'location',
        'dateandtime',
        'frequency',
        'explanation',
        'phase_status',
        'status_explanation',
        'sentiment',
        'created_at'
    ];

    // Use only created_at timestamp (updated_at column removed)
    public $timestamps = true;
    
    // Define only created_at timestamp
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * Get the resident that owns the complaint.
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class, 'user_id', 'user_id');
    }
}
