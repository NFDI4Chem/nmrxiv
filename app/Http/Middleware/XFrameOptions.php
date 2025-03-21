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
            return $response->header('Content-Security-Policy', 'frame-src data: blob: *');
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
