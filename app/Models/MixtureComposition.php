<?php

namespace App\Models;

use App\Enums\MixtureCompositionBasis;
use App\Enums\MixtureDeterminationMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MixtureComposition extends Model
{
    protected $fillable = [
        'sample_id',
        'basis',
        'determination_method',
        'nucleus',
        'relaxation_delay_s',
        'has_residual',
    ];

    protected function casts(): array
    {
        return [
            'basis' => MixtureCompositionBasis::class,
            'determination_method' => MixtureDeterminationMethod::class,
            'relaxation_delay_s' => 'decimal:3',
            'has_residual' => 'boolean',
        ];
    }

    public function sample(): BelongsTo
    {
        return $this->belongsTo(Sample::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(MixtureComponent::class, 'sample_id', 'sample_id')
            ->orderBy('sort_order');
    }

    public function isSpectrumVerifiable(): bool
    {
        if ($this->basis === null) {
            return false;
        }

        $components = $this->relationLoaded('components')
            ? $this->components
            : $this->components()->get();

        if ($components->isEmpty()) {
            return false;
        }

        return $components->every(
            fn (MixtureComponent $component): bool => filled($component->integrated_signal)
        );
    }
}
