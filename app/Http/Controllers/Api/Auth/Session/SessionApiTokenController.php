<?php

namespace App\Http\Controllers\Api\Auth\Session;

use App\Http\Controllers\Controller;
use App\Http\Resources\PersonalAccessTokenResource;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Contains api endpoint functions for admin related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 */
class SessionApiTokenController extends Controller
{
    
    public function index(Request $request)
    {
        $getApiTokens = $this->userAdminService->findApiTokensByParams(
            $request->user(),
            $request->get('sort', "id"),
            $request->get('order', "asc"),
            $request->get('count')
        );
        return PersonalAccessTokenResource::collection($getApiTokens);
    }

    public function show(Request $request)
    {
        return $this->sendSuccessResponse(
            "success",
            new PersonalAccessTokenResource($request->user()->currentAccessToken())
        );
    }

    public function store(Request $request)
    {
        return $this->sendSuccessResponse(
            "success",
            new PersonalAccessTokenResource(
                $this->userAdminService->createUserToken(
                    $request->user(),
                )
            )
        );
    }

    public function destroy(PersonalAccessToken $personalAccessToken, Request $request)
    {
        $delete = $this->userAdminService->deleteApiToken($personalAccessToken);
        if (!$delete) {
            return $this->sendErrorResponse(
                "Error deleting api token",
            );
        }
        return $this->sendSuccessResponse(
            "Api Token deleted.",
        );
    }

}
