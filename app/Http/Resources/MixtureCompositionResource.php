<?php

namespace App\Http\Resources;

use App\Support\Mixture\MixtureCompositionValidator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MixtureCompositionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $components = $this->relationLoaded('components')
            ? $this->components
            : collect();

        $total = MixtureCompositionValidator::sum(
            $components->pluck('value')
        );

        return [
            'id' => $this->id,
            'basis' => $this->basis?->value,
            'basis_label' => $this->basis?->unitLabel(),
            'basis_display_label' => $this->basis?->displayLabel(),
            'determination_method' => $this->determination_method?->value,
            'determination_method_label' => $this->determination_method?->displayLabel(),
            'nucleus' => $this->nucleus,
            'relaxation_delay_s' => $this->relaxation_delay_s,
            'has_residual' => (bool) $this->has_residual,
            'total' => $total,
            'spectrum_verifiable' => $this->isSpectrumVerifiable(),
            'sum_warning' => MixtureCompositionValidator::sumWarning(
                $components->pluck('value'),
                $this->basis,
                (bool) $this->has_residual
            ),
            'components' => MixtureComponentResource::collection($components),
        ];
    }
}
