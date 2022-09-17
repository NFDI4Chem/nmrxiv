<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Storage;

class Dataset extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasDOI;

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
        'study_id',
        'project_id',
        'draft_id',
        'fs_id',
        'dataset_photo_path',
        'nmrium_info',
        'license_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'public_url',
        'private_url',
        'dataset_photo_url',
    ];

    protected $hidden = [
        'nmrium_info',
    ];

    /**
     * Get the dataset identifier
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function identifier(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? 'NMRXIV:D'.$value : null,
        );
    }

    /**
     * Get the URL to the dataset's profile photo.
     *
     * @return string
     */
    public function getDatasetPhotoUrlAttribute()
    {
        return $this->dataset_photo_path
                    ? Storage::disk(env('FILESYSTEM_DRIVER_PUBLIC'))->url($this->dataset_photo_path)
                    : '';
    }

    protected function getPublicUrlAttribute()
    {
        return  env('APP_URL', null).'/datasets/'.urlencode($this->slug);
    }

    protected function getPrivateUrlAttribute()
    {
        return  env('APP_URL', null).'/datasets/'.urlencode($this->url);
    }

    public function study()
    {
        return $this->belongsTo(Study::class, 'study_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function draft()
    {
        return $this->belongsTo(Draft::class, 'draft_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'Team_id');
    }

    /**
     * Get the license of the dataset.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function license()
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'ILIKE', '%'.$search.'%')
                    ->orWhere('description', 'ILIKE', '%'.$search.'%');
            });
        })->when($filters['sort'] ?? 'newest', function ($query, $sort) {
            if ($sort === 'newest') {
                $query->orderByDesc('updated_at');
            } elseif ($sort === 'rating') {
                $query->orderByDesc('likes');
            } elseif ($sort === 'creation') {
                $query->orderByDesc('created_at');
            }
        });
    }
}
