<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintRequest extends Model
{
    use HasFactory;

    // Explicit table name
    protected $table = 'complaintrequests';

    // Use default 'id' as primary key
    // protected $primaryKey = 'user_id'; // Removed to allow multiple complaints per user

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
        'created_at',
        'photo',
        'video',
        // Additional fields for specific complaint types
        'items_stolen',
        'items_value',
        'business_name',
        'vehicle_details'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'date_occurrence' => 'datetime',
    ];
    
    /**
     * Specify attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'photo',
        'video',
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
