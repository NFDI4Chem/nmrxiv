<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmbargoReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'days_before_release',
        'sent_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Get the project that owns the embargo reminder.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
