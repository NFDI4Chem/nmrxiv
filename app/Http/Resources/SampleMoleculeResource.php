<?php

namespace App\Http\Resources;

use App\Models\Sample;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SampleMoleculeResource extends JsonResource
{
    public static $wrap = null;

    public function __construct(Sample $resource)
    {
        parent::__construct($resource);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Sample $sample */
        $sample = $this->resource;

        $sample->loadMissing(['molecules', 'mixtureComposition.components.molecule']);

        return [
            'molecules' => $sample->molecules,
            'mixture_composition' => $sample->mixtureComposition
                ? new MixtureCompositionResource($sample->mixtureComposition)
                : null,
        ];
    }
}
