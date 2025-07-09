<?php
namespace App\Helpers\Response;

use App\Enums\JWT\EncryptedResponse;
use App\Services\JWT\JWTService;

class ResponseHelpers
{

    public static function encryptedResponse(array $data): array
    {
        $payloadSecret = config('services.jwt.payload.secret');
        if (!$payloadSecret) {
            throw new \Exception('Payload secret is not configured');
        }
        $jwtService = app(JWTService::class);
        try {
            $jwtService->setSecret($payloadSecret);
            $encryptedData = $jwtService->jwtRawEncode($data);
        } catch (\Exception $e) {
            throw new \Exception('Error encrypting response data: ' . $e->getMessage());
        }

        return [
            EncryptedResponse::ENCRYPTED_RESPONSE_DATA->value => $encryptedData,
            EncryptedResponse::ENCRYPTED_RESPONSE->value => true,
        ];
    }

    public static function response(array $data, ?bool $encrypted = false): array
    {
        if ($encrypted) {
            return self::encryptedResponse($data);
        }

        return $data;


    }
}
