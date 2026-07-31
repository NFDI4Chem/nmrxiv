<?php

namespace App\Actions\Sample;

use App\Enums\MixtureCompositionBasis;
use App\Enums\MixtureDeterminationMethod;
use App\Models\MixtureComponent;
use App\Models\MixtureComposition;
use App\Models\Sample;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SyncMixtureComposition
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function syncMetadata(Sample $sample, array $attributes): MixtureComposition
    {
        $basis = MixtureCompositionBasis::from((string) $attributes['basis']);

        $composition = MixtureComposition::query()->updateOrCreate(
            ['sample_id' => $sample->id],
            [
                'basis' => $basis,
                'determination_method' => isset($attributes['determination_method'])
                    ? MixtureDeterminationMethod::from((string) $attributes['determination_method'])
                    : MixtureDeterminationMethod::Qnmr,
                'nucleus' => Arr::get($attributes, 'nucleus'),
                'relaxation_delay_s' => Arr::get($attributes, 'relaxation_delay_s'),
                'has_residual' => (bool) ($attributes['has_residual'] ?? false),
            ]
        );

        return $composition->fresh(['components.molecule']);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateMetadata(Sample $sample, array $attributes): MixtureComposition
    {
        $composition = $sample->mixtureComposition;

        if ($composition === null) {
            throw ValidationException::withMessages([
                'basis' => 'No mixture composition exists for this sample yet. Add mixture components first.',
            ]);
        }

        $composition->update([
            'basis' => MixtureCompositionBasis::from((string) $attributes['basis']),
            'determination_method' => isset($attributes['determination_method'])
                ? MixtureDeterminationMethod::from((string) $attributes['determination_method'])
                : $composition->determination_method,
            'nucleus' => Arr::get($attributes, 'nucleus'),
            'relaxation_delay_s' => Arr::get($attributes, 'relaxation_delay_s'),
            'has_residual' => (bool) ($attributes['has_residual'] ?? false),
        ]);

        return $composition->fresh(['components.molecule']);
    }

    /**
     * @param  array<string, mixed>  $componentAttributes
     */
    public function upsertComponent(
        Sample $sample,
        int $moleculeId,
        array $componentAttributes
    ): MixtureComponent {
        if (! $sample->molecules()->whereKey($moleculeId)->exists()) {
            throw ValidationException::withMessages([
                'value' => 'The molecule must be attached to the sample before recording its mixture share.',
            ]);
        }

        $existing = MixtureComponent::query()
            ->where('sample_id', $sample->id)
            ->where('molecule_id', $moleculeId)
            ->first();

        $attributes = [
            'value' => $componentAttributes['value'],
            'integrated_signal' => $componentAttributes['integrated_signal'] ?? null,
            'n_nuclei' => $componentAttributes['n_nuclei'] ?? null,
        ];

        if ($existing === null) {
            $attributes['sort_order'] = $componentAttributes['sort_order']
                ?? ((int) MixtureComponent::query()
                    ->where('sample_id', $sample->id)
                    ->max('sort_order') + 1);
        }

        $component = MixtureComponent::query()->updateOrCreate(
            [
                'sample_id' => $sample->id,
                'molecule_id' => $moleculeId,
            ],
            $attributes
        );

        return $component->fresh('molecule');
    }

    public function removeComponent(Sample $sample, int $moleculeId): void
    {
        MixtureComponent::query()
            ->where('sample_id', $sample->id)
            ->where('molecule_id', $moleculeId)
            ->delete();

        if (! MixtureComponent::query()->where('sample_id', $sample->id)->exists()) {
            MixtureComposition::query()->where('sample_id', $sample->id)->delete();
        }
    }

    public function clearForSample(Sample $sample): void
    {
        DB::transaction(function () use ($sample): void {
            MixtureComponent::query()->where('sample_id', $sample->id)->delete();
            MixtureComposition::query()->where('sample_id', $sample->id)->delete();
        });
    }
}
