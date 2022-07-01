<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
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
            'team' => $this->when(!$this->team->personal_team, $this->team),
            'owner' => new UserResource($this->owner),
            'photo_url' => $this->project_photo_path,
            'license' => new LicenseResource($this->license),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
