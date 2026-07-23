<?php

namespace App\Models;

use App\Traits\ResolvesNmrxivRouteBinding;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use OwenIt\Auditing\Contracts\Auditable;
use Storage;

class Dataset extends Model implements Auditable
{
    use HasDOI;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use ResolvesNmrxivRouteBinding;

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
        'study_id',
        'project_id',
        'draft_id',
        'fs_id',
        'dataset_photo_path',
        'license_id',
        'external_url',
        'assignments',
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

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'starred' => 'boolean',
            'is_public' => 'boolean',
            'assignments' => 'array',
            'spectra_is_ft' => 'boolean',
            'spectra_is_fid' => 'boolean',
            'spectra_info_extracted_at' => 'datetime',
        ];
    }

    /**
     * True when the dataset has user-supplied assignment content. We treat
     * any non-empty `acs` string OR any `atom_peaks` rows as "assigned",
     * which keeps the validator (`assignments|array|min:1`) and UI badges
     * in sync with what the user actually saved on the Assignments tab.
     */
    public function hasAssignments(): bool
    {
        $a = $this->assignments;
        if (! is_array($a)) {
            return false;
        }
        if (! empty($a['acs']) && trim((string) $a['acs']) !== '') {
            return true;
        }
        if (! empty($a['atom_peaks']) && is_array($a['atom_peaks']) && count($a['atom_peaks']) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get the dataset identifier
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
                    ? Storage::disk(config('filesystems.default_public'))->url($this->dataset_photo_path)
                    : '';
    }

    protected function getPublicUrlAttribute()
    {
        // return  env('APP_URL', null).'/datasets/'.urlencode($this->slug);
        return config('app.url').'/dataset/D'.$this->getRawOriginal('identifier');
    }

    protected function getPrivateUrlAttribute()
    {
        return config('app.url').'/datasets/'.urlencode($this->url);
    }

    public function study(): BelongsTo
    {
        return $this->belongsTo(Study::class, 'study_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function validation(): BelongsTo
    {
        return $this->belongsTo(Validation::class, 'validation_id');
    }

    public function draft(): BelongsTo
    {
        return $this->belongsTo(Draft::class, 'draft_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'Team_id');
    }

    public function nmrium(): MorphOne
    {
        return $this->morphOne(NMRium::class, 'nmriumable');
    }

    /**
     * NMRium workspace payload for this dataset, normalized for API and UI.
     * Molecule headers are merged from the study sample when present.
     *
     * @return array<string, mixed>|null
     */
    public function normalizedNmriumInfo(): ?array
    {
        $nmrium = $this->nmrium;
        if (! $nmrium) {
            return null;
        }

        $nmriumInfo = $nmrium->nmrium_info;
        if (is_string($nmriumInfo)) {
            $nmriumInfo = json_decode($nmriumInfo, true);
        }
        if (! is_array($nmriumInfo)) {
            $nmriumInfo = [];
        }
        if (! isset($nmriumInfo['data']) || ! is_array($nmriumInfo['data'])) {
            $nmriumInfo['data'] = [];
        }
        if (! isset($nmriumInfo['data']['molecules']) || ! is_array($nmriumInfo['data']['molecules'])) {
            $nmriumInfo['data']['molecules'] = [];
        }

        $sample = optional($this->study)->sample;
        if ($sample) {
            $nmriumInfo['data']['molecules'] = $sample
                ->mergeNmriumMolecules($nmriumInfo['data']['molecules']);
        }

        return $nmriumInfo;
    }

    /**
     * The directory/file backing this dataset.
     *
     * The canonical link is `datasets.fs_id -> file_system_objects.id`,
     * which is set atomically when the dataset is created from a folder
     * (see `App\Actions\Draft\ProcessDraft::createDatasetFromOrphanedFile`
     * and the chemotion path nearby). The reverse `file_system_objects.dataset_id`
     * back-pointer is only updated by a separate `save()` and gets out of
     * sync after re-archive, partial reseed, or any flow that recreates the
     * dataset row without re-walking the fs tree. Defining the relationship
     * as `belongsTo` on `fs_id` keeps it correct in those cases too.
     */
    public function fsObject(): BelongsTo
    {
        return $this->belongsTo(FileSystemObject::class, 'fs_id');
    }

    /**
     * Get the license of the dataset.
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'ILIKE', '%'.$search.'%')
                    ->orWhere('description', 'ILIKE', '%'.$search.'%')
                    ->orWhere('type', 'ILIKE', '%'.$search.'%');
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

    protected static function nmrxivRouteBindingNamespace(): string
    {
        return 'Dataset';
    }

    protected static function nmrxivRouteBindingPrefix(): string
    {
        return 'D';
    }
}
