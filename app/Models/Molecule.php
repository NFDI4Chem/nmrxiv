<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Get the molecule identifier
     */
    protected function identifier(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? 'NMRXIV:M'.$value : null,
        );
    }

    public function samples(): BelongsToMany
    {
        return $this->belongsToMany(Sample::class)
            ->withPivot('percentage_composition')
            ->withTimestamps();
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
