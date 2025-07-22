<?php

namespace App\Http\Middleware;

use App\Enums\JWT\EncryptedRequest as JWTEncryptedRequest;
use App\Services\JWT\JWTService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EncryptedRequest
{
    public function __construct(
        private JWTService $jwtService
    ){}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->has(JWTEncryptedRequest::ENCRYPTED_REQUEST->value)) {
            return $next($request);
        }
        if ($request->get(JWTEncryptedRequest::ENCRYPTED_REQUEST->value) !== true) {
            return $next($request);
        }

        if (!$request->has(JWTEncryptedRequest::ENCRYPTED_REQUEST_DATA->value)) {
            return response()->json(
                [
                    'error' => 'Encrypted request data is missing'
                ],
                400
            );
        }
        $data = $request->get(JWTEncryptedRequest::ENCRYPTED_REQUEST_DATA->value, null);
        if (!is_string($data)) {
            return response()->json(
                [
                    'error' => 'Encrypted request data must be a string'
                ],
                400
            );
        }

        $payloadSecret = config('services.jwt.payload.secret');
        if (!$payloadSecret) {
            return response()->json(
                [
                    'error' => 'Payload secret is not configured'
                ],
                500
            );
        }
        try {
            $this->jwtService->setSecret($payloadSecret);
            $decryptedData = $this->jwtService->jwtRawDecode($data);

            if (!isset($decryptedData[JWTEncryptedRequest::ENCRYPTED_REQUEST_PAYLOAD->value])) {
                return response()->json(
                    [
                        'error' => 'Decrypted request payload is missing'
                    ],
                    400
                );
            }
            $request->merge($decryptedData[JWTEncryptedRequest::ENCRYPTED_REQUEST_PAYLOAD->value] ?? []);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Failed to decrypt request data: ' . $e->getMessage()
                ],
                400
            );
        }
        return $next($request);
    }
}
