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
        'authors',
        'citation_text',
    ];

    /**
     * Projects that belongs to citations.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
