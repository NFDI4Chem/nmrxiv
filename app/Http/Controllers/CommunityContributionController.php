<?php

namespace App\Http\Controllers;

use App\Actions\Community\PublishCommunityStudies;
use App\Actions\Draft\GetOrCreateCommunityDraft;
use App\Http\Requests\PublishCommunityStudiesRequest;
use App\Models\Draft;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommunityContributionController extends Controller
{
    public function show(Request $request, GetOrCreateCommunityDraft $getOrCreateCommunityDraft): Response
    {
        $draft = $getOrCreateCommunityDraft->execute($request->user());

        return Inertia::render('CommunityContribution', [
            'draft' => $draft->only([
                'id',
                'key',
                'name',
                'settings',
                'current_step',
            ]),
        ]);
    }

    public function publishStudies(
        PublishCommunityStudiesRequest $request,
        Draft $draft,
        PublishCommunityStudies $publishCommunityStudies,
    ): JsonResponse {
        $result = $publishCommunityStudies->execute(
            $draft,
            array_map('intval', $request->validated('study_ids')),
        );

        return response()->json([
            'message' => 'Your selected samples have been queued for processing and will be published as independent public entries.',
            'study_ids' => $result['study_ids'],
        ]);
    }
}
