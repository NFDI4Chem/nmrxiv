<?php

namespace App\Models;

use App\Traits\CacheClear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Maize\Markable\Markable;
use Maize\Markable\Models\Like;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Tags\HasTags;
use Storage;

class Project extends Model implements Auditable
{
    use CacheClear;
    use Searchable;
    use Markable;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasTags;

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
        'draft_id',
        'fs_id',
        'project_photo_path',
        'license_id',
        'release_date',
    ];

    protected static $marks = [
        Like::class,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['public_url', 'private_url', 'project_photo_url'];

    /**
     * Get the URL to the project's profile photo.
     *
     * @return string
     */
    public function getProjectPhotoUrlAttribute()
    {
        return $this->project_photo_path
                    ? Storage::disk('minio_public')->url($this->project_photo_path)
                    : '';
    }

    public function studies()
    {
        return $this->hasMany(Study::class, 'project_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function nonPersonalTeam()
    {
        return $this->team()->where('personal_team', false);
    }

    public function draft()
    {
        return $this->belongsTo(Draft::class, 'draft_id');
    }

    public function likes()
    {
        return Like::count($this);
    }

    /**
     * Get the owner of the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all of the project's users including its owner.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allUsers()
    {
        return $this->users;
    }

    /**
     * Get all of the users that belong to the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('projectMembership');
    }

    /**
     * Determine if the given user belongs to the project.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function hasUser($user)
    {
        return $this->users->contains($user) || $user->ownsProject($this);
    }

    /**
     * Determine if the given email address belongs to a user on the project.
     *
     * @param  string  $email
     * @return bool
     */
    public function hasUserWithEmail(string $email)
    {
        return $this->allUsers()->contains(function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Get the user with the given email if belongs to the project
     *
     * @param  string  $email
     * @return bool
     */
    public function userWithEmail(string $email)
    {
        return $this->allUsers()->first(function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Get the user project role
     *
     * @param  string  $email
     * @return bool
     */
    public function userProjectRole(string $email)
    {
        $user = $this->userWithEmail($email);

        if ($user) {
            if ($user['projectMembership']) {
                return $user['projectMembership']['role'];
            } elseif ($this->owner_id == $user->id) {
                return 'owner';
            }
        }
    }

    // /**
    //  * Determine if the given user has the given permission on the project.
    //  *
    //  * @param  \App\Models\User  $user
    //  * @param  string  $permission
    //  * @return bool
    //  */
    // public function userHasPermission($user, $permission)
    // {
    //     return $user->hasProjectPermission($this, $permission);
    // }

    /**
     * Get all of the pending user invitations for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectInvitations()
    {
        return $this->hasMany(ProjectInvitation::class);
    }

    /**
     * Remove the given user from the project.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function removeUser($user)
    {
        $this->users()->detach($user);
    }

    protected function getPublicUrlAttribute()
    {
        return env('APP_URL', null).'/projects/'.$this->owner->username.'/'.urlencode($this->slug);
    }

    protected function getPrivateUrlAttribute()
    {
        return env('APP_URL', null).'/projects/'.urlencode($this->url);
    }

    /**
     * Get the license of the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function license()
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return $this->is_public;
    }

    /**
     * Authors that belongs to project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        })->when($filters['sort'] ?? null, function ($query, $sort) {
            if ($sort === 'newest') {
                $query->orderByDesc('updated_at');
            } elseif ($sort === 'rating') {
                $query->orderByDesc('likes');
            } elseif ($sort === 'creation') {
                $query->orderByDesc('created_at');
            }
        });
    }
    public function citations()
    {
        return $this->belongsToMany(Citation::class);
    }
}
