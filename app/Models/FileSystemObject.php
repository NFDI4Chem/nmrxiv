<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileSystemObject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
        'version_id',
        'version',
        'parent_id',
        'settings',
        'info',
        'level',
        'has_children'
    ];

    public function children()
    {
        return $this->hasMany(FileSystemObject::class, 'parent_id', 'id');
    }
}
