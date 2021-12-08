<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'start_time',
        'end_time',
        'user_id',
    ];

    
    /**
     * Announcement belongs to the user
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
