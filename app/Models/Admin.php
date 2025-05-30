<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'users'; // Use the users table
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'firstname',
        'lastname',
        'home_address',
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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthdate' => 'date',
        'verified' => 'boolean',
        'voter' => 'boolean',
        'age' => 'integer',
    ];

    /**
     * Scope to get only admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin')->orWhere('role', 'super_admin');
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        if ($this->firstname && $this->lastname) {
            return $this->firstname . ' ' . $this->lastname;
        }
        return $this->name ?? 'N/A';
    }

    /**
     * Get the position/role display name
     */
    public function getPositionAttribute()
    {
        return ucfirst($this->role);
    }
}
