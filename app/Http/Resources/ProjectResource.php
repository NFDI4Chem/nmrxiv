<?php

namespace App\Http\Resources;

use App\Models\FileSystemObject;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    private bool $lite = true;

    private array $properties = ['users', 'studies', 'files', 'authors', 'citations'];

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
            'team' => $this->when(($this->team && ! $this->team->personal_team), $this->team),
            'owner' => new UserResource($this->owner),
            'photo_url' => $this->project_photo_url,
            'is_public' => $this->is_public,
            'is_published' => $this->is_published,
            'identifier' => $this->identifier,
            'schema_version' => $this->schema_version,
            'public_url' => $this->public_url ? $this->public_url : null,
            'doi' => $this->doi,
            'tags' => $this->tags,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'download_url' => $this->download_url,
            'species' => json_decode($this->species),
            'stats' => [
                'likes' => $this->likesCount(),
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
                                        ->where([['project_id', $this->id], ['level', 0]])
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
                    $this->mergeWhen(
                        in_array('citations', $this->properties),
                        function () {
                            return [
                                'citations' => CitationResource::collection(
                                    $this->citations
                                ),
                            ];
                        }
                    ),
                ];
            }),
        ];
    }
}
