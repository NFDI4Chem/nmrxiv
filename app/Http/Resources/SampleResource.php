<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SampleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'molecules' => $this->molecules,
            'mixture_composition' => $this->when(
                $this->relationLoaded('mixtureComposition') && $this->mixtureComposition,
                fn () => new MixtureCompositionResource(
                    $this->mixtureComposition->loadMissing('components.molecule')
                )
            ),
            'submitted_through' => $this->submitted_through,
        ];
    }
}
