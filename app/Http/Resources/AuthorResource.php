<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    /**
     * Transform the author resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'given_name' => $this->given_name,
            'family_name' => $this->family_name,
            'full_name' => trim("{$this->given_name} {$this->family_name}"),
            'email_id' => $this->email_id,
            'orcid_id' => $this->orcid_id,
            'affiliation' => $this->affiliation,
            'contributor_type' => $this->whenPivotLoaded('author_project', function () {
                return $this->pivot->contributor_type;
            }),
            'sort_order' => $this->whenPivotLoaded('author_project', function () {
                return $this->pivot->sort_order;
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
