<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'orcid_id',
        'given_name',
        'family_name',
        'email_id',
        'affiliation',
        'role',
    ];

    /**
     * Projects that belongs to contributors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
