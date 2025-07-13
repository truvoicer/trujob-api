<?php

namespace App\Http\Controllers;

use App\Enums\JWT\EncryptedResponse;
use App\Helpers\Response\ResponseHelpers;
use App\Models\User;
use App\Services\Permission\AccessControlService;
use App\Services\User\UserAdminService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    protected ResponseHelpers $responseHelpers;
    protected AccessControlService $accessControlService;
    protected UserAdminService $userAdminService;

    public function __construct()
    {
        $this->accessControlService = app(AccessControlService::class);
        $this->userAdminService = app(UserAdminService::class);
        $this->responseHelpers = app(ResponseHelpers::class);
    }

    // protected function sendErrorResponse(string $message, ?array $data = [], ?array $errors = [], ?int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): \Illuminate\Http\JsonResponse
    // {
    //     return response()->json([
    //         'status' => 'error',
    //         'message' => $message,
    //         'data' => $data,
    //         'errors' => $errors,
    //     ], $statusCode);
    // }
    // protected function sendSuccessResponse(string $message, $data = [], ?array $errors = [], ?int $statusCode = Response::HTTP_OK): \Illuminate\Http\JsonResponse
    // {
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => $message,
    //         'data' => $data,
    //         'errors' => $errors,
    //     ], $statusCode);
    // }

    protected function sendJsonResponse(
        bool $encryptedResponse,
        string $message,
        $data = [],
        int $statusCode = Response::HTTP_OK
    ): \Illuminate\Http\JsonResponse {
        $responseData = [
            'message' => $message,
        ];
        if (!empty($data)) {
            $responseData['data'] = $data;
        }
        if ($encryptedResponse) {
            $responseData = $this->responseHelpers->encryptedResponse(
                $responseData
            );
        }
        return response()->json(
            $responseData,
            $statusCode
        );
    }
    protected function sendResourceResponse(
        bool $encryptedResponse,
        JsonResource $resource,
        int $statusCode = Response::HTTP_OK
    ): JsonResource {
        if ($encryptedResponse) {
            $resource->additional([
                EncryptedResponse::ENCRYPTED_RESPONSE->value => true
            ]);
        }
        $resource->response()->setStatusCode($statusCode);
        return $resource;
    }
}
