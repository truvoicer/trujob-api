<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSiteRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() instanceof Site) {
            return response()->json([
                'error' => 'Unauthorized, You must be an authenticated site to access this resource.',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
