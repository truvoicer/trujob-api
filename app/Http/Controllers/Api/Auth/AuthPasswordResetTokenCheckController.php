<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthPasswordResetTokenCheckRequest;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class AuthPasswordResetTokenCheckController extends Controller
{

    public function __invoke(AuthPasswordResetTokenCheckRequest $request): JsonResponse
    {
        [$site, $user] = SiteHelper::getCurrentSite();

        if (!$site instanceof Site) {
            return response()->json([
                'message' => 'Site not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $decodedToken = base64_decode($request->validated('token'));
        if ($decodedToken === false) {
            return response()->json([
                'message' => 'Invalid token format',
            ], Response::HTTP_BAD_REQUEST);
        }
        parse_str($decodedToken, $tokenData);
        if (empty($tokenData['email'])) {
            return response()->json([
                'message' => 'Email is missing from token',
            ], Response::HTTP_BAD_REQUEST);
        }
        if (empty($tokenData['token'])) {
            return response()->json([
                'message' => 'Token is missing from token',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $site->users()->where('email', $tokenData['email'])->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found for the provided email',
            ], Response::HTTP_NOT_FOUND);
        }

        $exists = Password::tokenExists(
            $user,
            $tokenData['token'],
        );

        return response()->json([
            'message' => $exists
                ? 'Token is valid'
                : 'Token is invalid or expired',
        ], $exists ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);

    }
}
