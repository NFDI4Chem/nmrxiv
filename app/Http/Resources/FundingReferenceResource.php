<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FundingReferenceResource extends JsonResource
{
    /**
     * Transform the funding reference resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'funder_name' => $this->funder_name,
            'funder_identifier' => $this->funder_identifier,
            'funder_identifier_type' => $this->funder_identifier_type,
            'award_number' => $this->award_number,
            'award_title' => $this->award_title,
            'award_uri' => $this->award_uri,
            'user_id' => data_get($this->resource, 'pivot.user'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
