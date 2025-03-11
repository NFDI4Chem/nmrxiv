<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_id');
    }

    /**
     * Define hasMany relation with studies
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studies(): HasMany
    {
        return $this->hasMany(Study::class, 'study_id');
    }

    /**
     * Define hasMany relation with datasets
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class, 'dataset_id');
    }
}
