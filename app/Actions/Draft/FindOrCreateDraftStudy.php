<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\FileSystemObject;
use App\Models\Project;
use App\Models\Study;
use App\Models\Validation;

class FindOrCreateDraftStudy
{
    public function __construct(
        private CreateDraftStudy $createDraftStudy
    ) {}

    /**
     * Find existing study or create a new one for draft processing.
     */
    public function execute(FileSystemObject $folder, Draft $draft, Project $project, Validation $validation): Study
    {
        // Try to find existing study
        $study = $this->findExistingStudy($draft, $folder);

        if (! $study) {
            $study = $this->createNewStudy($folder, $draft, $project, $validation);
            $this->updateFolderWithStudy($folder, $study);
        }

        return $study;
    }

    /**
     * Find existing study by draft and folder.
     */
    private function findExistingStudy(Draft $draft, FileSystemObject $folder): ?Study
    {
        return Study::where([
            ['draft_id', $draft->id],
            ['fs_id', $folder->id],
        ])->first();
    }

    /**
     * Create a new study for the given folder.
     */
    private function createNewStudy(FileSystemObject $folder, Draft $draft, Project $project, Validation $validation): Study
    {
        $input = [
            'name' => $folder->name,
            'description' => '',
            'team_id' => $project->team_id,
            'project_id' => $project->id,
            'owner_id' => $project->owner_id,
            'is_public' => false,
        ];

        return $this->createDraftStudy->create($input, $draft, $folder, $validation);
    }

    /**
     * Update folder with study relationship.
     */
    private function updateFolderWithStudy(FileSystemObject $folder, Study $study): void
    {
        $folder->study_id = $study->id;
        $folder->save();
    }
}
