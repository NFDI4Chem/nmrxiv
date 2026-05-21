<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     */
    public function owner(): BelongsTo
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
        $now = Carbon::now();

        return (new static)::where('status', 'active')->where(function ($q) use ($now) {
            $q->where('start_time', '<=', $now);
            $q->where('end_time', '>=', $now);
        })->get();
    }
}
