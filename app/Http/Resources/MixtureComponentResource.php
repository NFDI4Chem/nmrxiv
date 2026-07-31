<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MixtureComponentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'molecule_id' => $this->molecule_id,
            'value' => $this->value,
            'integrated_signal' => $this->integrated_signal,
            'n_nuclei' => $this->n_nuclei,
            'sort_order' => $this->sort_order,
            'molecule' => $this->whenLoaded('molecule', fn () => $this->molecule),
        ];
    }
}
