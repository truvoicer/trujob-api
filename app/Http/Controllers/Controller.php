<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Permission\AccessControlService;
use App\Services\User\UserAdminService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected AccessControlService $accessControlService;
    protected UserAdminService $userAdminService;

    public function __construct()
    {
        $this->accessControlService = app(AccessControlService::class);
        $this->userAdminService = app(UserAdminService::class);
    }

    protected function setAccessControlUser(?User $user = null) {
        if ($user instanceof User) {
            $this->accessControlService->setUser($user);
        }
    }
    protected function sendErrorResponse(string $message, ?array $data = [], ?array $errors = [], ?int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ], $statusCode);
    }
    protected function sendSuccessResponse(string $message, $data = [], ?array $errors = [], ?int $statusCode = Response::HTTP_OK): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
        ], $statusCode);
    }
}
