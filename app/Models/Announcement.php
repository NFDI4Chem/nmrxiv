<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'status',
        'start_time',
        'end_time',
        'user_id',
    ];

    /**
     * Get the owner of the announcement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the active announcements.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function active()
    {
        return (new static)::where('status', 'active')->where(function ($q) {
            $q->where('start_time', '<=', Carbon::now());
            $q->where('end_time', '>=', Carbon::now());
        })->get();
    }
}
