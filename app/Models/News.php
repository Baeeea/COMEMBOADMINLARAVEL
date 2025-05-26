<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'Title',
        'content',
        'createdAt',
        'image'
    ];

    protected $casts = [
        'createdAt' => 'datetime',
    ];

    // Disable Laravel's default timestamps if you're using custom createdAt
    public $timestamps = true;
}
