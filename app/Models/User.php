<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'firstname',
        'lastname',
        'username',
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthdate' => 'date',
            'verified' => 'boolean',
            'voter' => 'boolean',
            'age' => 'integer',
        ];
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

    /**
     * Relationship with feedback
     */
    public function feedbacks()
    {
        return $this->hasMany(\App\Models\Feedback::class);
    }

    /**
     * Get the user's avatar URL using different API services
     */
    public function getAvatarUrl($size = 150, $type = 'ui-avatars')
    {
        // If user has uploaded profile photo stored as BLOB, use direct REST API
        if ($this->profile) {
            // Use the direct API endpoint with cache-busting parameter
            return url("/api/profile_image.php?id={$this->id}&t=".time());
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
        $name = urlencode($this->name ?? 'User');
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
        $seed = urlencode($this->name ?? $this->email ?? 'user');
        return "https://api.dicebear.com/7.x/adventurer/svg?seed={$seed}&size={$size}";
    }

    /**
     * Get RoboHash URL (robot/monster avatars)
     */
    public function getRobohashUrl($size = 150)
    {
        $seed = urlencode($this->email ?? $this->name ?? 'user');
        return "https://robohash.org/{$seed}?size={$size}x{$size}";
    }

}
