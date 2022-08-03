<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function children()
    {
        return $this->hasMany(FileSystemObject::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(FileSystemObject::class, 'parent_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function draft()
    {
        return $this->belongsTo(Project::class, 'draft_id');
    }

    public function study()
    {
        return $this->belongsTo(Study::class, 'study_id');
    }
}
