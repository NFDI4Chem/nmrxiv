<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
