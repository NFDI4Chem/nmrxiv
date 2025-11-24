<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupportBubbleRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Spatie\SupportBubble\Events\SupportBubbleSubmittedEvent;

class SupportBubbleController extends Controller
{
    /**
     * Handle support bubble form submission
     */
    public function submit(SupportBubbleRequest $request): Response
    {
        try {
            // Fire the event
            event(new SupportBubbleSubmittedEvent(
                $request->input('subject'),
                $request->input('message'),
                $request->input('email'),
                $request->input('name'),
                $request->input('url'),
                $request->ip(),
                $request->userAgent(),
                $request
            ));

            return response()->view('support-bubble::success');

        } catch (\Exception $e) {
            Log::error('Support bubble submission failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'data' => $request->only(['email', 'subject']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.',
            ], 500);
        }
    }
}
