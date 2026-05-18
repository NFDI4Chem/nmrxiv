<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitationResource extends JsonResource
{
    /**
     * Transform the citation resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'doi' => $this->doi,
            'title' => $this->title,
            'authors' => $this->authors,
            'citation_text' => $this->citation_text,
            'user_id' => data_get($this->resource, 'pivot.user'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
