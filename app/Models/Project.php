<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Laravel\Scout\Searchable;
use App\Models\ProjectInvitation;
use Maize\Markable\Markable;
use Maize\Markable\Models\Like;
use Maize\Markable\Models\Favorite;
use Maize\Markable\Models\Bookmark;
use Maize\Markable\Models\Reaction;

class Project extends Model implements Auditable
{
    // use Searchable;
    use Markable;
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
        'project_photo_path',
    ];

    protected static $marks = [
        Like::class,
        Bookmark::class,
        Reaction::class,
        Favorite::class,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['public_url', 'private_url'];

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
        return $this->users->merge([$this->owner]);
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
            ->as('project-membership');
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

        if($user){
            if($user['project-membership']){
                return $user['project-membership']['role'];
            }elseIf($this->owner_id == $user->id){
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
     * Remove the given user from the team.
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
        return env('APP_URL', null) . '/projects/' . urlencode($this->slug);
    }

    protected function getPrivateUrlAttribute()
    {
        return env('APP_URL', null) . '/projects/' . urlencode($this->url);
    }
}
