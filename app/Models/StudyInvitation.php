<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyInvitation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'role',
        'message',
        'invited_by',
    ];

    /**
     * Get the study that the invitation belongs to.
     */
    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class);
    }
}
