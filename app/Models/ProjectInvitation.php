<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectInvitation extends Model
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
     * Get the project that the invitation belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
