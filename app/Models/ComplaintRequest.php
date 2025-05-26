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
        'firstname',
        'lastname',
        'middle_name',
        'birthdate',
        'age',
        'contact_number',
        'home_address',
        'complaint_type',
        'disturbance_type',
        'location',
        'dateandtime',
        'frequency',
        'specific_description',
        'status',
        'status_explanation',
        'sentiment',
        'created_at',
        'updated_at'
    ];

    // Use timestamps
    public $timestamps = true;
}
