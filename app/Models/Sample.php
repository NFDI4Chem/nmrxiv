<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'sample_type',
        'source',
        'isa',
        'study_id',
        'project_id'
    ];

    public function molecules()
    {
        return $this->belongsToMany(Molecule::class)
                    ->withPivot('percentage_composition')
                    ->withTimestamps();
    }

    /**
     * Get the study that owns the sample.
     */
    public function study()
    {
        return $this->belongsTo(Sample::class);
    }
}
