<?php

namespace App\Http\Resources;

use App\Models\FileSystemObject;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    private bool $lite = true;

    private array $properties = ['users', 'studies', 'files', 'authors'];

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
            'team' => $this->when(($this->team && ! $this->team->personal_team), $this->team),
            'owner' => new UserResource($this->owner),
            'photo_url' => $this->project_photo_url,
            'is_public' => $this->is_public,
            'tags' => $this->tags,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'stats' => [
                'likes' => $this->likes(),
            ],
            'license' => new LicenseResource(
                $this->license
            ),
            $this->mergeWhen(! $this->lite, function () {
                return [
                    $this->mergeWhen(
                        in_array('users', $this->properties),
                        function () {
                            return [
                                'users' => UserResource::collection(
                                    $this->allUsers()
                                ),
                            ];
                        }
                    ),
                    $this->mergeWhen(
                        in_array('studies', $this->properties),
                        function () {
                            return [
                                'studies' => StudyResource::collection(
                                    $this->studies
                                ),
                            ];
                        }
                    ),
                    $this->mergeWhen(
                        in_array('files', $this->properties),
                        function () {
                            return [
                                'files' => [
                                    'name' => '/',
                                    'has_children' => true,
                                    'children' => FileSystemObject::with(
                                        'children'
                                    )
                                        ->where([['project_id', $this->id]])
                                        ->orderBy('type')
                                        ->get(),
                                ],
                            ];
                        }
                    ),
                    $this->mergeWhen(
                        in_array('authors', $this->properties),
                        function () {
                            return [
                                'authors' => AuthorResource::collection(
                                    $this->authors
                                ),
                            ];
                        }
                    ),
                ];
            }),
        ];
    }
}
