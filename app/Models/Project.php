<?php

namespace App\Models;

use App\Events\DraftProcessed;
use App\Events\ProjectArchival;
use App\Events\ProjectDeletion;
use App\Notifications\ProjectDeletionFailureNotification;
use App\Notifications\ProjectDeletionReminderNotification;
use App\Traits\CacheClear;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Notification;
use Laravel\Scout\Searchable;
use Maize\Markable\Markable;
use Maize\Markable\Models\Bookmark;
use Maize\Markable\Models\Like;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Tags\HasTags;
use Storage;

class Project extends Model implements Auditable
{
    use CacheClear;
    use HasDOI;
    use HasFactory;
    use HasTags;
    use Markable;
    use \OwenIt\Auditing\Auditable;
    use Searchable;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'starred',
        'location',
        'is_public',
        'obfuscationcode',
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
        'deleted_on',
        'species',
    ];

    protected static $marks = [
        Like::class,
        Bookmark::class,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['public_url', 'private_url', 'project_photo_url', 'is_bookmarked', 'is_published'];

    /**
     * Get the URL to the project's profile photo.
     *
     * @return string
     */
    public function getProjectPhotoUrlAttribute()
    {
        return $this->project_photo_path
                    ? Storage::disk(env('FILESYSTEM_DRIVER_PUBLIC'))->url($this->project_photo_path)
                    : '';
    }

    public function getIsPublishedAttribute()
    {
        if ($this->is_public) {
            return true;
        } else {
            if ($this->release_date && $this->doi) {
                return Carbon::now()->startOfDay()->gte($this->release_date);
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * check if the project is bookmarked by current user
     *
     * @return string
     */
    public function getIsBookmarkedAttribute()
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        } else {
            return Bookmark::has($this, $user);
        }
    }

    /**
     * Get the project identifier
     */
    protected function identifier(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? 'NMRXIV:P'.$value : null,
        );
    }

    public function studies(): HasMany
    {
        return $this->hasMany(Study::class, 'project_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function nonPersonalTeam()
    {
        return $this->team()->where('personal_team', false);
    }

    public function draft(): BelongsTo
    {
        return $this->belongsTo(Draft::class, 'draft_id');
    }

    public function likesCount()
    {
        return Like::count($this);
    }

    /**
     * Get the owner of the project.
     */
    public function owner(): BelongsTo
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
     */
    public function users(): BelongsToMany
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
     */
    public function projectInvitations(): HasMany
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
        // return env('APP_URL', null).'/projects/'.$this->owner->username.'/'.urlencode($this->slug);
        return env('APP_URL', null).'/P'.$this->getRawOriginal('identifier');
    }

    protected function getPrivateUrlAttribute()
    {
        return env('APP_URL', null).'/projects/'.urlencode($this->url);
    }

    /**
     * Get the license of the project.
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    public function validation(): BelongsTo
    {
        return $this->belongsTo(Validation::class, 'validation_id');
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        if ($this->is_public && ! $this->is_archived) {
            return true;
        }
    }

    /**
     * Authors that belongs to project.
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class)
            ->withPivot('contributor_type', 'sort_order')->orderBy('sort_order', 'asc');
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

    public function citations(): BelongsToMany
    {
        return $this->belongsToMany(Citation::class);
    }

    /**
     * Send Notification via email.
     *
     * @param  string  $notifyType  (deletion / deletionReminder / archival / archivalAdmin)
     * @param  array  sendTo
     * @return void
     */
    public function sendNotification($notifyType, $sendTo)
    {
        switch ($notifyType) {
            case 'deletion':
                event(new ProjectDeletion($this, $sendTo));
                break;
            case 'deletionReminder':
                Notification::send($sendTo, new ProjectDeletionReminderNotification($this));
                break;
            case 'archival':
                event(new ProjectArchival($this, $sendTo));
                break;
            case 'deletionFailure':
                Notification::send($sendTo, new ProjectDeletionFailureNotification($this));
                break;
            case 'publish':
                event(new DraftProcessed($this, $sendTo));
                break;
        }
    }
}
