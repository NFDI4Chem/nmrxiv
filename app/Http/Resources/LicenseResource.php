<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LicenseResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'spdx_id' => $this->spdx_id,
            'url' => $this->url,
            'html_url' => $this->html_url,
            'permissions' => $this->permissions,
            'description' => $this->description,
        ];
    }
}
