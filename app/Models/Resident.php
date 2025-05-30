<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'residents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'home_address',
        'user_id',
        'middle_name',
        'gender',
        'religion',
        'voter',
        'resident_status',
        'working_status',
        'student_status',
        'age',
        'contact_number',
        'verified',
        'profile',
        'validIDFront',
        'validIDBack',
        'birthdate'
    ];    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'verified' => 'boolean',
            'voter' => 'boolean',
            'age' => 'integer',
        ];
    }

    /**
     * Get the resident's full name.
     */
    public function getFullNameAttribute()
    {
        if ($this->firstname && $this->lastname) {
            return $this->firstname . ' ' . $this->lastname;
        }
        return $this->username ?? 'N/A';
    }

    /**
     * Debug method to check if attribute exists in the model
     */
    public function hasAttribute($attribute)
    {
        return array_key_exists($attribute, $this->attributes);
    }

    /**
     * Get raw attribute value without any mutators
     */
    public function getRawAttribute($attribute)
    {
        return $this->getOriginal($attribute);
    }
}
