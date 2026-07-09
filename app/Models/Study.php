<?php

namespace App\Models;

use App\Traits\CacheClear;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;
use Maize\Markable\Markable;
use Maize\Markable\Models\Bookmark;
use Maize\Markable\Models\Like;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Tags\HasTags;

class Study extends Model implements Auditable
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
        'is_archived',
        'obfuscationcode',
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
        'species',
        'authors',
        'citations',
        'molecules',
        'submitted_through',
        'external_id',
        'external_url',
        'processing_logs',
        'tracking_item_name',
        'doi',
        'identifier',
        'validation_id',
        'metadata_bagit_generation_status',
        'metadata_bagit_generation_logs',
        'has_nmrium',
        'has_nmredata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'citations' => 'array',
            'molecules' => 'array',
            'processing_logs' => 'array',
            'metadata_bagit_generation_logs' => 'array',
            'starred' => 'boolean',
            'is_public' => 'boolean',
            'is_archived' => 'boolean',
        ];
    }

    protected static $marks = [
        Like::class,
        Bookmark::class,
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
        'is_published',
        'is_bookmarked',
    ];

    public function getIsPublishedAttribute()
    {
        if ($this->is_public) {
            return true;
        } else {
            if ($this->project) {
                return $this->project ? $this->project->is_published : false;
            } else {
                if ($this->release_date && $this->doi) {
                    return Carbon::now()->startOfDay()->gte($this->release_date);
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Get the study identifier
     */
    protected function identifier(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? 'NMRXIV:S'.$value : null,
        );
    }

    /**
     * Get the URL to the study's profile photo.
     *
     * @return string
     */
    public function getStudyPhotoUrlAttribute()
    {
        return $this->study_photo_path
                    ? Storage::disk(config('filesystems.default_public'))->url($this->study_photo_path)
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
        foreach ($dataset_urls as $dataset_url) {
            if ($dataset_url) {
                array_push($urls, $dataset_url);
            }
        }

        return $urls;
    }

    /**
     * Get the experiment types performed on this sample.
     *
     * @return string
     */
    public function getStudyExperimentTypesAttribute()
    {
        $experiment_types = $this->datasets->pluck('type');

        return $experiment_types;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    protected function getPublicUrlAttribute()
    {
        // return env('APP_URL', null).'/projects/'.$this->owner->username.'/'.urlencode($this->project->slug).'?tab=study&id='.$this->slug;
        return config('app.url').'/sample/S'.$this->getRawOriginal('identifier');
    }

    protected function getPrivateUrlAttribute()
    {
        return config('app.url').'/studies/'.urlencode($this->url);
    }

    public function draft(): BelongsTo
    {
        return $this->belongsTo(Draft::class, 'draft_id');
    }

    public function fsObject(): HasOne
    {
        return $this->hasOne(FileSystemObject::class);
    }

    public function validation(): BelongsTo
    {
        return $this->belongsTo(Validation::class, 'validation_id');
    }

    /**
     * Get all of the pending user invitations for the study.
     */
    public function studyInvitations(): HasMany
    {
        return $this->hasMany(StudyInvitation::class);
    }

    public function nmrium(): MorphOne
    {
        return $this->morphOne(NMRium::class, 'nmriumable');
    }

    /**
     * Get the user study role
     *
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

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class)->orderBy('name');
    }

    /**
     * Get the user with the given email if belongs to the study
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
     * Get the owner of the project.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all of the study's users including its owner.
     *
     * @return Collection
     */
    public function allUsers()
    {
        return $this->project ? $this->project->users->merge($this->users) : $this->users->merge([$this->owner]);
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
     * Get the sample associated with the study.
     */
    public function sample(): HasOne
    {
        return $this->hasOne(Sample::class, 'study_id');
    }

    /**
     * Whether the study sample has at least one assignable chemical structure.
     */
    public function hasAssignedStructure(): bool
    {
        $this->loadMissing('sample.molecules');

        if (! $this->sample) {
            return false;
        }

        return $this->sample->molecules->contains(
            fn (Molecule $molecule) => filled($molecule->canonical_smiles)
                || filled($molecule->smiles)
                || filled($molecule->absolute_smiles)
                || filled($molecule->sdf)
        );
    }

    public function isReadyForCommunityPublish(): bool
    {
        return $this->internal_status === 'complete'
            && $this->has_nmrium
            && $this->hasAssignedStructure();
    }

    /**
     * Studies still visible in a draft workspace (excludes submitted samples).
     *
     * @param  Builder<Study>  $query
     * @return Builder<Study>
     */
    public function scopeOnDraftWorkspace(Builder $query, Draft $draft): Builder
    {
        return $query->where('is_public', false)
            ->where(function (Builder $workspaceQuery) use ($draft): void {
                $workspaceQuery->where('draft_id', $draft->id)
                    ->orWhereHas('datasets', function (Builder $datasetQuery) use ($draft): void {
                        $datasetQuery->where('draft_id', $draft->id);
                    });
            });
    }

    public function molecules()
    {
        return $this->sample()->molecules();
    }

    /**
     * Remove the given user from the study.
     *
     * @param  User  $user
     * @return void
     */
    public function removeUser($user)
    {
        $this->users()->detach($user);
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return $this->is_public && ! $this->is_archived;
    }

    /**
     * Get all of the users that belong to the study.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps()
            ->as('studyMembership');
    }

    /**
     * Get the license of the study.
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    /**
     * Get all of the authors that belong to the study.
     */
    public function studyAuthors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class)
            ->withPivot('contributor_type', 'sort_order')->orderBy('sort_order', 'asc');
    }

    /**
     * Normalized citations (pivot `citation_study`). Named `linkedCitations` to avoid clashing with the JSON `citations` attribute.
     */
    public function linkedCitations(): BelongsToMany
    {
        return $this->belongsToMany(Citation::class)->withTimestamps();
    }

    /**
     * Accessor for authors attribute (alias for studyAuthors relationship).
     */
    protected function authors(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->studyAuthors,
        );
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'ILIKE', '%'.$search.'%')
                    ->orWhere('description', 'ILIKE', '%'.$search.'%');
            });
        })->when($filters['sort'] ?? null, function ($query, $sort) {
            if ($sort === 'newest') {
                $query->orderByDesc('updated_at');
            } elseif ($sort === 'creation') {
                $query->orderByDesc('created_at');
            }
        });
    }

    /**
     * check if the study is bookmarked by current user
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
}
