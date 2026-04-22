<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LicenseResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'spdx_id' => $this->spdx_id,
            'url' => $this->url,
            'html_url' => $this->html_url,
            'permissions' => $this->permissions,
            'description' => $this->description,
            'body' => $this->body,
        ];
    }
}
