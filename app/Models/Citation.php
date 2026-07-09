<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Citation extends Model
{
    use HasFactory;

    protected $fillable = [
        'doi',
        'title',
        'title_slug',
        'authors',
        'citation_text',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Projects that belong to citations.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * Studies linked to this citation (normalized pivot; distinct from JSON `studies.citations`).
     */
    public function studies(): BelongsToMany
    {
        return $this->belongsToMany(Study::class);
    }
}
