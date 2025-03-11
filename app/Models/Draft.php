<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Draft extends Model
{
    use HasFactory;
    use HasTags;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'relative_url',
        'path',
        'key',
        'is_deleted',
        'owner_id',
        'team_id',
        'settings',
        'info',
        'project_enabled',
        'current_step',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(FileSystemObject::class);
    }

    /**
     * Get the owner of the draft.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
