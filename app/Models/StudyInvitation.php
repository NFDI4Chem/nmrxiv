<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * Get the study that the invitation belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function study()
    {
        return $this->belongsTo(Study::class);
    }
}
