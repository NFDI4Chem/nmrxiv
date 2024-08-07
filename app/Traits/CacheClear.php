<?php

namespace App\Traits;

use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use Artisan;
use Illuminate\Support\Facades\Cache;

trait CacheClear
{
    /**
     * Boot function for Laravel model events.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * After model is created, or whatever action, clear cache.
         */
        static::updated(function ($model) {
            if ($model instanceof Project) {
                Cache::forget('projects');
                Cache::forget('stats.projects');
            } elseif ($model instanceof Study) {
                Cache::forget('studies');
                Cache::forget('stats.compounds');
            } elseif ($model instanceof Dataset) {
                if ($model instanceof Dataset) {
                    $project = $model->study->project;
                }
                Cache::forget('projects.'.$project->slug);
                Cache::forget('stats.techniques');
                Cache::forget('stats.spectra');
            } else {
                Artisan::call('cache:clear');
            }
        });
    }
}
