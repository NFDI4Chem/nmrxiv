<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One detected 1D NMR signal of a dataset, indexed for spectrum search.
 */
class DatasetSignal extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'dataset_id',
        'spectrum_index',
        'nucleus',
        'shift',
        'multiplicity',
        'intensity',
        'is_solvent',
        'source',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'spectrum_index' => 'integer',
            'shift' => 'float',
            'intensity' => 'float',
            'is_solvent' => 'boolean',
        ];
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }
}
