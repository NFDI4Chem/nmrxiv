<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use HasFactory;

    /**
     * Projects that belongs to authors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    protected $fillable = [
        'title',
        'orcid_id',
        'given_name',
        'family_name',
        'email_id',
        'affiliation',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
