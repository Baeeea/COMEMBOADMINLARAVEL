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
        'profile_photo',
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

}
