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

        if ($request->route()->getName() == 'embed') {
            // Enhanced CSP for embed routes - more restrictive for security
            $csp = "frame-ancestors *; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob: https:; font-src 'self' data:";

            return $response->header('Content-Security-Policy', $csp);
        } else {
            if ($response instanceof \Illuminate\Http\Response) {
                $xframeOptions = 'SAMEORIGIN';

                return $response->header('X-Frame-Options', $xframeOptions);
            } else {
                return $response;
            }
        }
    }
}
