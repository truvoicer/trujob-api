<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthPasswordResetRequest;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class AuthPasswordResetController extends Controller
{
    /**
     * Show the form for resetting the password.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $token): \Illuminate\Http\JsonResponse
    {
        return response()->json(['token' => $token]);
    }

    public function store(AuthPasswordResetRequest $request): \Illuminate\Http\JsonResponse
    {

        [$site, $user] = SiteHelper::getCurrentSite();
        if (!$user instanceof User) {
            $user = User::where('email', $request->validated('email'))->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }
        }
        if (!$site instanceof Site) {
            return response()->json([
                'message' => 'Site not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $site = $user->sites()->updateExistingPivot($site->id, ['password_reset' => true]);

        if (!$site) {
            return response()->json([
                'message' => 'User does not belong to the specified site',
            ], Response::HTTP_FORBIDDEN);
        }

        $status = Password::sendResetLink(
            $request->validated()
        );

        return response()->json([
            'message' => $status === Password::RESET_LINK_SENT
                ? 'Password reset link sent'
                : 'Failed to send password reset link',
        ], $status === Password::RESET_LINK_SENT ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
