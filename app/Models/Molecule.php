<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Molecule extends Model
{
    use HasFactory;

    protected $fillable = [
        'cas',
        'molecular_formula',
        'molecular_weight',
        'smiles',
        'absolute_smiles',
        'canonical_smiles',
        'inchi',
        'standard_inchi',
        'inchi_key',
        'standard_inchi_key',
        'COMMENT',
        'doi',
        'sdf',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['public_url'];

    protected function casts(): array
    {
        return [
            'workspace_experiment_type_counts' => 'array',
            'has_public_spectra' => 'boolean',
            'public_samples_count' => 'integer',
            'public_experiment_type_counts' => 'array',
            'public_catalog_indexed_at' => 'datetime',
            'annotation_level' => 'integer',
            'quality_breakdown' => 'array',
            'quality_rubric_version' => 'integer',
            'quality_scored_at' => 'datetime',
        ];
    }

    /**
     * Get the molecule identifier
     */
    protected function identifier(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? 'NMRXIV:M'.$value : null,
        );
    }

    protected function getPublicUrlAttribute()
    {
        return config('app.url').'/compound/M'.$this->getRawOriginal('identifier');
    }

    public function samples(): BelongsToMany
    {
        return $this->belongsToMany(Sample::class)
            ->withPivot('percentage_composition')
            ->withTimestamps();
    }

    public function teamQualityScores(): HasMany
    {
        return $this->hasMany(TeamMoleculeQualityScore::class);
    }

    public function studies()
    {
        $samples = $this->samples->load('study');
        $studies = [];
        foreach ($samples as $sample) {
            if ($sample->study) {
                array_push($studies, $sample->study->id);
            }
        }

        return Study::whereIn('id', $studies);
    }
}
