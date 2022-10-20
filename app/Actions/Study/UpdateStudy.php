<?php

namespace App\Actions\Study;

use App\Models\Study;
use App\Models\Ticker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UpdateStudy
{
    /**
     * Create a study.
     *
     * @param  array  $input
     * @return \App\Models\Study
     */
    public function update(Study $study, array $input)
    {
        $errorMessages = [
            'license.required_if' => 'The license field is required when the study is made public.',
        ];
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'license' => ['required_if:is_public,"true"'],
        ], $errorMessages)->validate();

        return DB::transaction(function () use ($input, $study) {
            $study->forceFill([
                'name' => $input['name'],
                'slug' => Str::slug($input['name'], '-'),
                'description' => $input['description'] ? $input['description'] : $study->description,
                'color' => array_key_exists('color', $input) ? $input['color'] : $study->color,
                'starred' => array_key_exists('starred', $input) ? $input['starred'] : $study->starred,
                'location' => array_key_exists('location', $input) ? $input['location'] : $study->location,
                'url' => array_key_exists('url', $input) ? $input['url'] : $study->url,
                'type' => array_key_exists('type', $input) ? $input['type'] : $study->type,
                'access' => array_key_exists('access', $input) ? $input['access'] : 'restricted',
                'access_type' => array_key_exists('access_type', $input) ? $input['access_type'] : 'viewer',
                'is_public' => array_key_exists('is_public', $input) ? $input['is_public'] : $study->is_public,
                'study_photo_path' => array_key_exists('study_photo_path', $input) ? $input['study_photo_path'] : $study->study_photo_path,
                'license_id' => array_key_exists('license_id', $input) ? $input['license_id'] : $study->license_id,
            ])->save();

            if (array_key_exists('tags_array', $input)) {
                $study->syncTagsWithType(array_filter($input['tags_array']), 'Study');
            }

            $is_public = array_key_exists('is_public', $input) ? $input['is_public'] : $study->is_public;
            $release_date = array_key_exists('release_date', $input) ? $input['release_date'] : $study->release_date;
            $licenseExists = array_key_exists('license_id', $input);
            $license_id = $licenseExists ? $input['license_id'] : $study->license_id;

            if ($is_public == true) {
                $release_date = Carbon::now()->timestamp;

                $sample = $study->sample;
                $sampleIdentifier = $sample->identifier ? $sample->identifier : null;

                if ($sampleIdentifier == null) {
                    $sampleTicker = Ticker::whereType('sample')->first();
                    $sampleIdentifier = $sampleTicker->index + 1;
                    $sample->identifier = $sampleIdentifier;
                    $sample->save();

                    $sampleTicker->index = $sampleIdentifier;
                    $sampleTicker->save();
                }

                $molecules = $sample->molecules;

                foreach ($molecules as $molecule) {
                    $moleculeIdentifier = $molecule->identifier ? $molecule->identifier : null;
                    if ($moleculeIdentifier == null) {
                        $moleculeTicker = Ticker::whereType('molecule')->first();
                        $moleculeIdentifier = $moleculeTicker->index + 1;
                        $molecule->identifier = $moleculeIdentifier;
                        $molecule->save();

                        $moleculeTicker->index = $moleculeIdentifier;
                        $moleculeTicker->save();
                    }
                }
            }

            $datasets = $study->datasets;
            foreach ($datasets as $dataset) {
                if ($licenseExists) {
                    if ($dataset->license_id == null) {
                        $dataset->license_id = $license_id;
                    }
                }
                if ($is_public) {
                    $dsIdentifier = $dataset->identifier ? $dataset->identifier : null;

                    if ($dsIdentifier == null) {
                        $dsTicker = Ticker::whereType('dataset')->first();
                        $dsIdentifier = $dsTicker->index + 1;
                        $dataset->identifier = $dsIdentifier;

                        $dsTicker->index = $dsIdentifier;
                        $dsTicker->save();
                    }

                    $dataset->is_public = $is_public;
                    $dataset->release_date = $release_date;
                }
                $dataset->save();
            }
        });
    }
}
