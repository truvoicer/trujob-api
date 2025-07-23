<?php

namespace App\Http\Middleware;

use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSiteUserRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $siteUser = $request->user();
        if (!$siteUser instanceof SiteUser) {
            return response()->json([
                'error' => 'Unauthorized, You must be an authenticated site user to access this resource.',
            ], Response::HTTP_UNAUTHORIZED);
        }
        if (!$siteUser?->site instanceof Site) {
            return response()->json([
                'error' => 'Unauthorized, Site not found.',
            ], Response::HTTP_UNAUTHORIZED);
        }
        if (!$siteUser->user instanceof User) {
            return response()->json([
                'error' => 'Unauthorized, User not found.',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
