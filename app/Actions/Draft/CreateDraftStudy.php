<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Sample;
use App\Models\Study;
use App\Models\User;
use App\Models\Validation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateDraftStudy
{
    /**
     * Create a study for draft processing.
     */
    public function create(array $input, Draft $draft, ?FileSystemObject $folder = null, ?Validation $validation = null): Study
    {
        $this->validateInput($input);

        return DB::transaction(function () use ($input, $draft, $folder, $validation) {
            $study = Study::create([
                'name' => $input['name'],
                'slug' => Str::slug($input['name'], '-'),
                'description' => $input['description'] ?? '',
                'color' => $input['color'] ?? null,
                'starred' => $input['starred'] ?? null,
                'location' => $input['location'] ?? null,
                'obfuscationcode' => Str::random(40),
                'type' => $input['type'] ?? null,
                'uuid' => Str::uuid(),
                'access' => $input['access'] ?? 'restricted',
                'access_type' => $input['access_type'] ?? 'viewer',
                'team_id' => $input['team_id'],
                'project_id' => $input['project_id'],
                'owner_id' => $input['owner_id'],
                'is_public' => $input['is_public'] ?? false,
                'license_id' => $input['license_id'] ?? null,
                'study_photo_path' => $input['study_photo_path'] ?? null,
                // Draft-specific fields
                'draft_id' => $draft->id,
                'fs_id' => $folder?->id,
            ]);

            // Associate validation if provided
            if ($validation) {
                $study->validation()->associate($validation);
                $study->save();
            }

            // Create sample for the study
            $this->createStudySample($study);

            // Attach user with creator role
            $user = User::find($input['owner_id']);
            if ($user) {
                $study->users()->attach($user, ['role' => 'creator']);
            }

            return $study;
        });
    }

    /**
     * Validate the input for creating a draft study.
     */
    private function validateInput(array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['required'],
            'owner_id' => ['required'],
        ])->validate();
    }

    /**
     * Create sample for study.
     */
    private function createStudySample(Study $study): void
    {
        $sample = Sample::create([
            'name' => $study->name.'_sample',
            'slug' => Str::slug($study->name.'_sample', '-'),
            'study_id' => $study->id,
            'project_id' => $study->project->id,
        ]);
        $study->sample()->save($sample);
    }
}
