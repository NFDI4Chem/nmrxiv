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
            $response->header('Content-Security-Policy', "default-src 'self'; base-uri 'self'; block-all-mixed-content; frame-src data: blob: *; img-src 'self'; style-src 'unsafe-inline' *;");
        }

        return $response;
    }
}
