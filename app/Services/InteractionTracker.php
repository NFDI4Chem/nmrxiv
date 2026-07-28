<?php

namespace App\Services;

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class InteractionTracker
{
    private const SESSION_VIEWS_KEY = 'tracked_views';

    private const SESSION_DOWNLOADS_KEY = 'tracked_downloads';

    public function recordView(Request $request, bool $reviewerPreview, Project|Study|Dataset|null $entity): void
    {
        if ($reviewerPreview || $entity === null || ! $this->isPubliclyCountable($entity)) {
            return;
        }

        $sessionKey = $this->sessionKeyForEntity($entity);

        if ($this->alreadyTracked($request, self::SESSION_VIEWS_KEY, $sessionKey)) {
            return;
        }

        $this->markTracked($request, self::SESSION_VIEWS_KEY, $sessionKey);
        $this->incrementCounter($entity, 'views');
    }

    public function recordDownloadFromIdentifier(Request $request, string $identifier): void
    {
        $resolved = resolveIdentifier($identifier);
        $model = $resolved['model'];

        if (! $model instanceof Project && ! $model instanceof Study && ! $model instanceof Dataset) {
            return;
        }

        if (! $this->isPubliclyCountable($model)) {
            return;
        }

        $sessionKey = $this->sessionKeyForEntity($model);

        if ($this->alreadyTracked($request, self::SESSION_DOWNLOADS_KEY, $sessionKey)) {
            return;
        }

        $this->markTracked($request, self::SESSION_DOWNLOADS_KEY, $sessionKey);
        $this->incrementCounter($model, 'downloads');
    }

    private function incrementCounter(Project|Study|Dataset $entity, string $column): void
    {
        $target = $this->resolveCounterTarget($entity);

        if ($target === null) {
            return;
        }

        $target::query()->whereKey($target->getKey())->increment($column);
    }

    private function resolveCounterTarget(Project|Study|Dataset $entity): ?Model
    {
        if ($entity instanceof Project) {
            return $entity;
        }

        if ($entity instanceof Study) {
            $project = $entity->relationLoaded('project')
                ? $entity->project
                : $entity->project()->first();

            return $project ?? $entity;
        }

        $entity->loadMissing(['project', 'study.project']);

        $project = $entity->project ?? $entity->study?->project;

        if ($project) {
            return $project;
        }

        return $entity->study;
    }

    private function isPubliclyCountable(Project|Study|Dataset $entity): bool
    {
        if ($entity instanceof Project) {
            return (bool) $entity->is_public;
        }

        if ($entity instanceof Study) {
            if (! $entity->is_public) {
                return false;
            }

            $project = $entity->relationLoaded('project')
                ? $entity->project
                : $entity->project()->first();

            return $project === null || (bool) $project->is_public;
        }

        if (! $entity->is_public) {
            return false;
        }

        $entity->loadMissing(['project', 'study']);

        $study = $entity->study;

        if ($study === null || ! $study->is_public) {
            return false;
        }

        $project = $entity->project ?? $study->project;

        return $project === null || (bool) $project->is_public;
    }

    private function sessionKeyForEntity(Project|Study|Dataset $entity): string
    {
        $prefix = match (true) {
            $entity instanceof Project => 'P',
            $entity instanceof Study => 'S',
            default => 'D',
        };

        return $prefix.$entity->getRawOriginal('identifier');
    }

    private function alreadyTracked(Request $request, string $sessionKey, string $entityKey): bool
    {
        /** @var list<string> $tracked */
        $tracked = $request->session()->get($sessionKey, []);

        return in_array($entityKey, $tracked, true);
    }

    /**
     * @return list<string>
     */
    private function markTracked(Request $request, string $sessionKey, string $entityKey): void
    {
        /** @var list<string> $tracked */
        $tracked = $request->session()->get($sessionKey, []);
        $tracked[] = $entityKey;
        $request->session()->put($sessionKey, $tracked);
    }
}
