<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XFrameOptions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $option = 'SAMEORIGIN';

        $xframeOptions = $xframeOptions = env('X_FRAME_OPTIONS', 'SAMEORIGIN');

        if ($request->routeIs('iframe') && $xframeOptions) {
            if (strpos($xframeOptions, 'ALLOW-FROM') !== false) {
                $url = trim(str_replace('ALLOW-FROM', '', $xframeOptions));

                $response->header('Content-Security-Policy', 'frame-ancestors '.$url);
            }
        }

        return $response->header('X-Frame-Options', $xframeOptions);
    }
}
