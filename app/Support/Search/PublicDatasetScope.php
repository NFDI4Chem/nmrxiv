<?php

namespace App\Support\Search;

use Illuminate\Database\Eloquent\Builder;

class PublicDatasetScope
{
    public static function apply(Builder $query): void
    {
        $query
            ->where('is_public', true)
            ->where('is_archived', false)
            ->where('is_deleted', false)
            ->whereHas('study', function (Builder $studyQuery): void {
                $studyQuery
                    ->where('is_public', true)
                    ->where('is_archived', false);
            })
            ->where(function (Builder $scopeQuery): void {
                $scopeQuery
                    ->whereNull('project_id')
                    ->orWhereHas('project', function (Builder $projectQuery): void {
                        $projectQuery
                            ->where('is_public', true)
                            ->where('is_archived', false)
                            ->where('is_deleted', false);
                    });
            });
    }
}
