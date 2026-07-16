<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MixtureComponent extends Model
{
    protected $fillable = [
        'sample_id',
        'molecule_id',
        'value',
        'integrated_signal',
        'n_nuclei',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
            'n_nuclei' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function sample(): BelongsTo
    {
        return $this->belongsTo(Sample::class);
    }

    public function molecule(): BelongsTo
    {
        return $this->belongsTo(Molecule::class);
    }
}
