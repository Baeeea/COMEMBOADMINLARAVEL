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
    
    /**
     * Get the admin's avatar URL using different API services
     */
    public function getAvatarUrl($size = 150, $type = 'ui-avatars', $debug = false)
    {
        // If admin has uploaded profile photo stored as BLOB, use our Laravel route
        if ($this->profile) {
            // Add version for cache control based on updated_at timestamp
            $version = $this->updated_at ? $this->updated_at->timestamp : time();
            // Use route() helper to generate the URL properly with parameters
            return route('profile.image', [
                'id' => $this->id, 
                'size' => $size, 
                'debug' => $debug ? '1' : '0',
                'v' => $version
            ]);
        }

        // Fallback to API-generated avatars
        switch ($type) {
            case 'gravatar':
                return $this->getGravatarUrl($size);
            case 'dicebear':
                return $this->getDiceBearUrl($size);
            case 'robohash':
                return $this->getRobohashUrl($size);
            case 'ui-avatars':
            default:
                return $this->getUIAvatarsUrl($size);
        }
    }

    /**
     * Get UI Avatars URL (initials-based)
     */
    public function getUIAvatarsUrl($size = 150)
    {
        $name = urlencode($this->name ?? 'Admin');
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF&size={$size}";
    }

    /**
     * Get Gravatar URL (email-based)
     */
    public function getGravatarUrl($size = 150)
    {
        $email = md5(strtolower(trim($this->email ?? '')));
        return "https://www.gravatar.com/avatar/{$email}?d=identicon&s={$size}";
    }

    /**
     * Get DiceBear Avatars URL (cute illustrated avatars)
     */
    public function getDiceBearUrl($size = 150)
    {
        $seed = urlencode($this->name ?? $this->email ?? 'admin');
        return "https://api.dicebear.com/7.x/adventurer/svg?seed={$seed}&size={$size}";
    }

    /**
     * Get RoboHash URL (robot/monster avatars)
     */
    public function getRobohashUrl($size = 150)
    {
        $seed = urlencode($this->email ?? $this->name ?? 'admin');
        return "https://robohash.org/{$seed}?size={$size}x{$size}";
    }
}
