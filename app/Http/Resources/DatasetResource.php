<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DatasetResource extends JsonResource
{
    private bool $lite = true;

    private array $properties = [];

    public function lite(bool $lite, array $properties): self
    {
        $this->lite = $lite;
        $this->properties = $properties;

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
            'project' => $this->project->slug,
            'study' => $this->study->slug,
            'owner' => new UserResource($this->owner),
            'dataset_photo_url' => $this->dataset_photo_url,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
