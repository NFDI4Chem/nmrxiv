<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyResource extends JsonResource
{
    private bool $lite = true;

    private array $properties = ['sample', 'users', 'license', 'authors'];

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
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'molecules' => $this->sample ? $this->sample->molecules : [],
            'team' => $this->when(! ($this->team && $this->team->personal_team), $this->team),
            'photo_url' => $this->study_photo_url,
            'tags' => $this->tags,
            'identifier' => $this->identifier,
            'doi' => $this->doi,
            'created_at' => $this->created_at,
            'release_date' => $this->release_date,
            'is_public' => $this->is_public,
            'public_url' => $this->public_url ? $this->public_url : null,
            'updated_at' => $this->updated_at,
            'study_preview_urls' => $this->study_preview_urls,
            'experiment_types' => $this->study_experiment_types,
            'download_url' => $this->download_url,
            'has_nmrium' => $this->has_nmrium,
            'submitted_through' => $this->submitted_through,
            'external_id' => $this->external_id,
            'external_url' => $this->external_url,
            'processing_logs' => $this->processing_logs,
            $this->mergeWhen(! $this->lite, function () {
                return [
                    $this->mergeWhen(
                        in_array('owner', $this->properties),
                        function () {
                            return [
                                'owner' => new UserResource($this->owner),
                            ];
                        }
                    ),
                ];
            }),
            $this->mergeWhen(! $this->lite, function () {
                return [
                    $this->mergeWhen(
                        in_array('sample', $this->properties),
                        function () {
                            return [
                                'sample' => new SampleResource($this->sample),
                            ];
                        }
                    ),
                ];
            }),
            $this->mergeWhen(! $this->lite, function () {
                return [
                    $this->mergeWhen(
                        in_array('users', $this->properties),
                        function () {
                            return [
                                'users' => UserResource::collection($this->allUsers()),
                            ];
                        }
                    ),
                ];
            }),
            $this->mergeWhen(! $this->lite, function () {
                return [
                    $this->mergeWhen(
                        in_array('datasets', $this->properties),
                        function () {
                            return [
                                'datasets' => DatasetResource::collection($this->datasets),
                            ];
                        }
                    ),
                ];
            }),
            $this->mergeWhen(! $this->lite, function () {
                return [
                    $this->mergeWhen(
                        in_array('license', $this->properties),
                        function () {
                            return [
                                'license' => new LicenseResource($this->license),
                            ];
                        }
                    ),
                ];
            }),
            $this->mergeWhen(! $this->lite, function () {
                return [
                    $this->mergeWhen(
                        in_array('authors', $this->properties),
                        function () {
                            return [
                                'authors' => $this->studyAuthors ? AuthorResource::collection($this->studyAuthors) : [],
                            ];
                        }
                    ),
                ];
            }),
            $this->mergeWhen(! $this->lite, function () {
                return [
                    $this->mergeWhen(
                        in_array('citations', $this->properties),
                        function () {
                            return [
                                'citations' => CitationResource::collection(
                                    $this->relationLoaded('linkedCitations') ? $this->linkedCitations : collect()
                                ),
                            ];
                        }
                    ),
                ];
            }),
        ];
    }
}
