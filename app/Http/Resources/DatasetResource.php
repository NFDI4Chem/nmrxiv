<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DatasetResource extends JsonResource
{
    private bool $lite = true;

    private array $properties = [];

    public function lite(bool $lite, ?array $properties = []): self
    {
        $this->lite = $lite;
        if ($properties && count($properties) > 0) {
            $this->properties = $properties;
        }

        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => $this->type,
            'identifier' => $this->identifier,
            'doi' => $this->doi,
            'project' => [
                'name' => $this->project ? $this->project->name : '',
                'public_url' => $this->project ? $this->project->public_url : null,
            ],
            'study' => [
                'name' => $this->study ? $this->study->name : '',
                'public_url' => $this->study ? $this->study->public_url : null,
            ],
            'owner' => new UserResource($this->owner),
            'dataset_photo_url' => $this->dataset_photo_url,
            'is_public' => $this->is_public,
            'public_url' => $this->public_url ? $this->public_url : null,
            'has_nmrium' => $this->has_nmrium,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
