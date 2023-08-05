<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Molecule extends Model
{
    use HasFactory;

    protected $fillable = [
        'CAS_NUMBER',
        'FORMULA',
        'MOLECULAR_WEIGHT',
        'SMILES',
        'SMILES_CHIRAL',
        'CANONICAL_SMILES',
        'INCHI',
        'STANDARD_INCHI',
        'INCHI_KEY',
        'STANDARD_INCHI_KEY',
        'fp0',
        'fp1',
        'fp2',
        'fp3',
        'fp4',
        'fp5',
        'fp6',
        'fp7',
        'fp8',
        'fp9',
        'fp10',
        'fp11',
        'fp12',
        'fp13',
        'fp14',
        'fp15',
        'DBE',
        'SSSR',
        'SAR',
        'COMMENT',
        'fORMULA',
        'MULTIPLICITY_0',
        'MULTIPLICITY_1',
        'MULTIPLICITY_2',
        'MULTIPLICITY_3',
        'VIEWS',
        'DOI',
        'MOL',
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

    public function samples()
    {
        return $this->belongsToMany(Sample::class)
                ->withPivot('percentage_composition')
                ->withTimestamps();
    }
}
