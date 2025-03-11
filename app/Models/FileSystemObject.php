<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileSystemObject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uuid',
        'slug',
        'description',
        'relative_url',
        'path',
        'type',
        'key',
        'compressionInfo',
        'kernelSessionInfo',
        'color',
        'starred',
        'is_public',
        'is_deleted',
        'is_archived',
        'is_original',
        'is_verified',
        'is_processed',
        'is_root',
        'sort_order',
        'owner_id',
        'project_id',
        'study_id',
        'dataset_id',
        'draft_id',
        'version_id',
        'version',
        'parent_id',
        'settings',
        'info',
        'level',
        'has_children',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['download_url'];

    /**
     * Get the download URL to the file system object.
     *
     * @return string
     */
    public function getDownloadUrlAttribute()
    {
        if ($this->model_type == 'study') {
            return $this->study ? $this->study->download_url : null;
        }
    }

    public function children(): HasMany
    {
        return $this->hasMany(FileSystemObject::class, 'parent_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FileSystemObject::class, 'parent_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function draft(): BelongsTo
    {
        return $this->belongsTo(Draft::class, 'draft_id');
    }

    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class, 'study_id');
    }
}
