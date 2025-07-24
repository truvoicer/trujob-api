<?php

namespace Tests\Helpers;

use App\Services\JWT\JWTService;
use Illuminate\Testing\Assert;

class ResponseTestHelpers
{
    public static function assertEncryptedResponse(
        $response,
    ): void {

        $responseJson = $response->json();
        if (!empty($responseJson['encrypted_response_data'])) {

            $response->assertJsonStructure([
                'encrypted_response_data',
                'encrypted_response'
            ]);
            $encryptedData = $responseJson['encrypted_response_data'];
        } else if (!empty($responseJson['data']['encrypted_response_data'])) {
            $response->assertJsonStructure([
                'data',
                'encrypted_response'
            ]);
            Assert::assertArrayHasKey('encrypted_response_data', $responseJson['data']);
            Assert::assertArrayHasKey('encrypted_response', $responseJson);
            $encryptedData = $responseJson['data']['encrypted_response_data'];
        } else {
            throw new \Exception('Encrypted response data not found');
        }
        $payloadSecret = config('services.jwt.payload.secret');
        if (!$payloadSecret) {
            throw new \Exception('Payload secret is not configured');
        }
        $jwtService = app(JWTService::class);

        $jwtService->setSecret($payloadSecret);
        $decryptedData = $jwtService->jwtRawDecode($encryptedData);
        Assert::assertArrayHasKey('payload', $decryptedData);
    }

    public static function extractEncryptedResponseData(
        $response,
    ): array|null {
        $responseJson = $response->json();
        
        if (!empty($responseJson['encrypted_response_data'])) {
            $encryptedData = $responseJson['encrypted_response_data'];
        } else if (!empty($responseJson['data']['encrypted_response_data'])) {
            $encryptedData = $responseJson['data']['encrypted_response_data'];
        } else {
            throw new \Exception('Encrypted response data not found');
        }
        $payloadSecret = config('services.jwt.payload.secret');
        if (!$payloadSecret) {
            throw new \Exception('Payload secret is not configured');
        }
        $jwtService = app(JWTService::class);

        $jwtService->setSecret($payloadSecret);
        $decryptedData = $jwtService->jwtRawDecode($encryptedData);

        return $decryptedData['payload'] ?? null;
    }
}
