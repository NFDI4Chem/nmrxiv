<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Study extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'starred',
        'location',
        'is_public',
        'url',
        'description',
        'type',
        'uuid',
        'access', 
        'access_type',
        'team_id',
        'owner_id',
        'project_id',
        'study_photo_path'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
