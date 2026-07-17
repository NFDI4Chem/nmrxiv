<?php

namespace App\Http\Controllers;

use App\Services\InteractionTracker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InteractionTrackingController extends Controller
{
    public function trackDownload(Request $request, string $identifier, InteractionTracker $interactionTracker): Response
    {
        $interactionTracker->recordDownloadFromIdentifier($request, $identifier);

        return response()->noContent();
    }
}
