<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use OwenIt\Auditing\Contracts\Auditable;
use Storage;

class Dataset extends Model implements Auditable
{
    use HasDOI;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

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
                    ? Storage::disk(env('FILESYSTEM_DRIVER_PUBLIC'))->url($this->dataset_photo_path)
                    : '';
    }

    protected function getPublicUrlAttribute()
    {
        // return  env('APP_URL', null).'/datasets/'.urlencode($this->slug);
        return env('APP_URL', null).'/D'.$this->getRawOriginal('identifier');
    }

    protected function getPrivateUrlAttribute()
    {
        return env('APP_URL', null).'/datasets/'.urlencode($this->url);
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

    public function validation()
    {
        return $this->belongsTo(Validation::class, 'validation_id');
    }

    public function draft()
    {
        return $this->belongsTo(Draft::class, 'draft_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'Team_id');
    }

    public function nmrium(): MorphOne
    {
        return $this->morphOne(NMRium::class, 'nmriumable');
    }

    public function fsObject()
    {
        return $this->hasOne(FileSystemObject::class);
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

    /**
     * Get NMRium info from a dataset.
     *
     * @param  App\Models\Dataset  $dataset
     * @return object $info
     */
    public static function getNMRiumInfo($dataset)
    {
        $info = null;
        $nmrium = $dataset->nmrium;
        if (! $nmrium) {
            $study = $dataset->study;
            if ($study->nmrium) {
                $NMRiumInfo = json_decode($study->nmrium->nmrium_info);
                foreach ($NMRiumInfo->data->spectra as $spectra) {
                    $fileSource = $spectra->sourceSelector->files[0];
                    $fileName = pathinfo($fileSource);
                    if ($fileName['basename'] == $dataset->fsObject->name) {
                        $info = $spectra->info;
                    }
                }
            }
        } else {
            $NMRiumInfo = json_decode($nmrium->nmrium_info);
            $spectra = $NMRiumInfo->data->spectra[0];
            $info = $spectra->info;
        }

        return $info;
    }

    /**
     * Extract NMRium info into an array.
     *
     * @param  App\Models\Dataset  $dataset
     * @return array $array
     */
    public static function extractNMRiumInfo($dataset)
    {
        $info = self::getNMRiumInfo($dataset);
        $dict = [
            'solvent' => null,
            'nucleus' => null,
            'dimension' => null,
            'probeName' => null,
            'experiment' => null,
            'temperature' => null,
            'baseFrequency' => null,
            'temperature' => null,
            'baseFrequency' => null,
            'fieldStrength' => null,
            'numberOfScans' => null,
            'pulseSequence' => null,
            'spectralWidth' => null,
            'numberOfPoints' => null,
            'relaxationTime' => null,
        ];

        if ($info) {
            if (property_exists($info, 'solvent')) {
                $dict['solvent'] = $info->solvent;
            }
            if (property_exists($info, 'nucleus')) {
                if (is_string($info->nucleus)) {
                    $dict['nucleus'] = [$info->nucleus];
                } else {
                    $dict['nucleus'] = $info->nucleus;
                }
            }
            if (property_exists($info, 'dimension')) {
                $dict['dimension'] = $info->dimension;
            }
            if (property_exists($info, 'probeName')) {
                $dict['probeName'] = $info->probeName;
            }
            if (property_exists($info, 'experiment')) {
                $dict['experiment'] = $info->experiment;
            }
            if (property_exists($info, 'temperature')) {
                $dict['temperature'] = $info->temperature;
            }
            if (property_exists($info, 'baseFrequency')) {
                $dict['baseFrequency'] = $info->baseFrequency;
            }
            if (property_exists($info, 'fieldStrength')) {
                $dict['fieldStrength'] = $info->fieldStrength;
            }
            if (property_exists($info, 'numberOfScans')) {
                $dict['numberOfScans'] = $info->numberOfScans;
            }
            if (property_exists($info, 'pulseSequence')) {
                $dict['pulseSequence'] = $info->pulseSequence;
            }
            if (property_exists($info, 'spectralWidth')) {
                $dict['spectralWidth'] = $info->spectralWidth;
            }
            if (property_exists($info, 'numberOfPoints')) {
                $dict['numberOfPoints'] = $info->numberOfPoints;
            }
            if (property_exists($info, 'relaxationTime')) {
                $dict['relaxationTime'] = $info->relaxationTime;
            }

            return $dict;
        }
    }
}
