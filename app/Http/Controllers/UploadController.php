<?php

namespace App\Http\Controllers;

use App\Actions\Draft\ProcessDraft;
use App\Models\Draft;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UploadController extends Controller
{
    public function __construct(private ProcessDraft $processDraft) {}

    public function upload(Request $request)
    {
        $draftId = $request->get('draft_id');

        if (is_numeric($draftId)) {
            $draft = Draft::find((int) $draftId);

            if ($draft && $request->user()?->can('updateDraft', $draft)) {
                $project = $this->processDraft->resolveDraftProject($draft);

                if ($project && $project->status !== 'draft') {
                    return redirect()->route('publish', ['draft' => $draft->id]);
                }
            }
        }

        return Inertia::render('Upload', [
            'draft_id' => $draftId,
            'step' => $request->get('step'),
            'deposition' => $request->get('deposition'),
        ]);
    }

    public function publish(Request $request, Draft $draft)
    {
        $this->authorize('updateDraft', $draft);

        $project = $this->processDraft->resolveDraftProject($draft);

        if (! $project) {
            return redirect()->route('upload', ['draft_id' => $draft->id]);
        }

        if (! Gate::forUser(Auth::user())->check('updateProject', $project)) {
            throw new AuthorizationException;
        }

        $validation = $project->validation;
        $validation->process(project: $project);

        $project->load([
            'studies.datasets',
            'studies.sample.molecules',
            'studies.sample.mixtureComposition.components.molecule',
            'owner',
            'citations',
            'fundingReferences',
            'authors',
            'tags',
            'license',
        ]);

        return Inertia::render('Publish', [
            'draft' => $draft,
            'project' => $project,
            'validation' => $validation,
        ]);
    }
}
