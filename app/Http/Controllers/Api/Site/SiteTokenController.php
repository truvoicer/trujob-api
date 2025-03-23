<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\NewSiteTokenRequest;
use App\Http\Resources\AccessTokenResource;
use App\Models\Site;
use App\Services\Site\SiteService;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class SiteTokenController extends Controller
{
    public function __construct(
        private SiteService $siteService,
    )
    {
    }

    public function create(Site $site, NewSiteTokenRequest $request): \Illuminate\Http\JsonResponse|AccessTokenResource
    {
        $token = $this->siteService->createToken($site, $request->validated('expires_at', null));

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating token',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return new AccessTokenResource($token);
    }

    public function destroy(Site $site, PersonalAccessToken $personalAccessToken): \Illuminate\Http\JsonResponse|AccessTokenResource
    {
        if (!$this->siteService->deleteApiToken($personalAccessToken)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting token',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Token deleted',
        ]);
    }
}
