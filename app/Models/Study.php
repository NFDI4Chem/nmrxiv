<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\StudyInvitation;
use App\Traits\CacheClear;

class Study extends Model implements Auditable
{
    use CacheClear;
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
        'draft_id',
        'owner_id',
        'project_id',
        'fs_id',
        'study_photo_path'
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

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    protected function getPublicUrlAttribute()
    {
        return  env('APP_URL', null)."/projects/".urlencode($this->slug);
    }

    protected function getPrivateUrlAttribute()
    {
        return  env('APP_URL', null)."/projects/".urlencode($this->url);
    }

    public function draft()
    {
        return $this->belongsTo(Draft::class, 'draft_id');
    }

    public function fsObject()
    {
        return $this->hasOne(FileSystemObject::class, 'fs_id');
    }

    /**
     * Get all of the pending user invitations for the study.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studyInvitations()
    {
        return $this->hasMany(StudyInvitation::class);
    }

    public function molecules()
    {
        return $this->belongsToMany(Molecule::class);
    }

    /**
     * Get the user study role
     *
     * @param  string  $email
     * @return bool
     */
    public function userStudyRole(string $email)
    {
        $user = $this->userWithEmail($email);
        if($user){
            if($user['studyMembership']){
                return $user['studyMembership']['role'];
            }elseIf($user['projectMembership']){
                return $user['projectMembership']['role'];
            }elseIf($this->owner_id == $user->id){
                return 'owner';
            }
        }
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function datasets()
    {
        return $this->hasMany(Dataset::class);
    }

    public function molecules()
    {
        return $this->hasMany(Molecule::class);
    }

    /**
     * Get the user with the given email if belongs to the study
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
     * Get the owner of the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all of the study's users including its owner.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allUsers()
    {
        return $this->project->users->merge($this->users);
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
     * Get all of the users that belong to the study.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('studyMembership');
    }
}
