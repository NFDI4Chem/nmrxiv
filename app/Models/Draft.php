<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Draft extends Model
{
    use HasFactory;
    use HasTags;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'relative_url',
        'path',
        'key',
        'is_deleted',
        'owner_id',
        'team_id',
        'settings',
        'info'
    ];

    public function files()
    {
        return $this->hasMany(FileSystemObject::class);
    }
}
