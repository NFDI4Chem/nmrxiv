<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    /**
     * Projects that belongs to authors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    protected $fillable = [
        'orcid_id',
        'given_name',
        'family_name',
        'email_id',
        'affiliation',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
