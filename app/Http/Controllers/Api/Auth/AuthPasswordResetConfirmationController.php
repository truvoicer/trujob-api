<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthPasswordResetConfirmationRequest;
use App\Models\Site;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class AuthPasswordResetConfirmationController extends Controller
{

    public function __invoke(AuthPasswordResetConfirmationRequest $request): \Illuminate\Http\JsonResponse
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

        if (!$user instanceof User) {
            $user = $site->users()->where('email', $tokenData['email'])->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found for the provided email',
                ], Response::HTTP_NOT_FOUND);
            }
        }
        $site = $user->sites()->updateExistingPivot($site->id, ['password_reset' => false]);

        if (!$site) {
            return response()->json([
                'message' => 'User does not belong to the specified site',
            ], Response::HTTP_FORBIDDEN);
        }

        $status = Password::reset(
            [
                'email' => $tokenData['email'],
                'password' => $request->validated('password'),
                'password_confirmation' => $request->validated('password_confirmation'),
                'token' => $tokenData['token'],
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json([
            'message' => $status === Password::PASSWORD_RESET
                ? __('Your password has been reset successfully.')
                : __('There was an error resetting your password.'),
        ], $status === Password::PASSWORD_RESET ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}
