<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    /**
     * Define hasMany relation with projects
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_id');
    }

    /**
     * Define hasMany relation with studies
     */
    public function studies(): HasMany
    {
        return $this->hasMany(Study::class, 'study_id');
    }

    /**
     * Define hasMany relation with datasets
     */
    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class, 'dataset_id');
    }
}
