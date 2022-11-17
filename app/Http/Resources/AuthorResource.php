<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
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
            'given_name' => $this->given_name,
            'family_name' => $this->family_name,
            'email_id' => $this->email_id,
            'orcid_id' => $this->orcid_id,
            'affiliation' => $this->affiliation,
            'pivot' => $this->pivot,
        ];
    }
}
