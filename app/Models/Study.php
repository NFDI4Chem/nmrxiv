<?php

namespace App\Models;

use App\Traits\CacheClear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Tags\HasTags;
use Storage;

class Study extends Model implements Auditable
{
    use CacheClear;
    use HasFactory;
    use HasTags;
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
        'study_photo_path',
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
        'study_photo_url',
        'study_preview_urls',
    ];

    /**
     * Get the URL to the study's profile photo.
     *
     * @return string
     */
    public function getStudyPhotoUrlAttribute()
    {
        return $this->study_photo_path
                    ? Storage::disk('minio_public')->url($this->study_photo_path)
                    : '';
    }

    /**
     * Get the URL to the study's datasets preview.
     *
     * @return string
     */
    public function getStudyPreviewUrlsAttribute()
    {
        $dataset_urls = $this->datasets->pluck('dataset_photo_url');
        $urls = [];
        foreach($dataset_urls as $dataset_url){
            if($dataset_url){
                array_push($urls, $dataset_url);
            }
        }
        return $urls;
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    protected function getPublicUrlAttribute()
    {
        return  env('APP_URL', null).'/projects/'.urlencode($this->slug);
    }

    protected function getPrivateUrlAttribute()
    {
        return  env('APP_URL', null).'/projects/'.urlencode($this->url);
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

    /**
     * Get the user study role
     *
     * @param  string  $email
     * @return bool
     */
    public function userStudyRole(string $email)
    {
        $user = $this->userWithEmail($email);
        if ($user) {
            if ($user['studyMembership']) {
                return $user['studyMembership']['role'];
            } elseif ($user['projectMembership']) {
                return $user['projectMembership']['role'];
            } elseif ($this->owner_id == $user->id) {
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
     * Get the sample associated with the study.
     */
    public function sample()
    {
        return $this->hasOne(Sample::class, 'study_id');
    }

    /**
     * Get all of the deployments for the project.
     */
    public function molecules()
    {
        return $this->sample()->molecules();
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

    /**
     * Get the license of the study.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function license()
    {
        return $this->belongsTo(License::class, 'license_id');
    }
}
