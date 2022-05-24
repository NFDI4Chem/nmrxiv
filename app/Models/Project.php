<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Laravel\Scout\Searchable;

class Project extends Model implements Auditable
{
    use Searchable;
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
        'project_photo_path'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'public_url',
        'private_url',
    ];

    public function studies()
    {
        return $this->hasMany(Study::class, 'project_id');
    }

    protected function getPublicUrlAttribute()
    {
        return  env('APP_URL', null)."/projects/".urlencode($this->slug);
    }

    protected function getPrivateUrlAttribute()
    {
        return  env('APP_URL', null)."/projects/".urlencode($this->url);
    }

}
