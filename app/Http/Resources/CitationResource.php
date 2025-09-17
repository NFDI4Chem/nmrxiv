<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CitationResource extends JsonResource
{
    /**
     * Transform the citation resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
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
            'user_id' => $this->whenPivotLoaded('citation_project', function () {
                return $this->pivot->user ?? null;
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
