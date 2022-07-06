<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Project;
use App\Http\Resources\StudyResource;
use Illuminate\Support\Facades\Storage;

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
            'photo_url' => $this->project_photo_path ? Storage::disk('minio_public')->url($this->project_photo_path) : '',
            'tags' => $this->tags,
            'license' => new LicenseResource($this->license),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'users'    => UserResource::collection($this->allUsers()),
            'studies' => StudyResource::collection($this->studies),
            'stats'=> [
                'likes' => $this->likes()
            ]
        ];
    }
}
