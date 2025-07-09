<?php

namespace App\Services\JWT;

use App\Exceptions\JWT\JWTException;
use Illuminate\Support\Carbon;

class JWTService
{

    const TOKEN = 'token';
    const ISSUED_AT = 'iat';
    const EXPIRES_AT = 'exp';
    const TOKEN_DATA_HEADER = 'header';
    const TOKEN_DATA_PAYLOAD = 'payload';
    const TOKEN_DATA_SIGNATURE = 'signature';

    private string $secret;
    private array $defaultPayload;
    private array $requiredTokenDataKeys = ['header', 'payload', 'signature'];

    private const DEFAULT_HEADER = [
        "alg" => "HS256",
        "typ" => "JWT"
    ];


    private function encodeHeader(array $header = self::DEFAULT_HEADER) {
        return $this->base64UrlEncode(json_encode($header));
    }

    private function encodePayload(array $payload) {
        return $this->base64UrlEncode(json_encode($payload));
    }

    private function buildSignatureHash(string $encodedHeader, string $encodedPayload, string $secret) {
        return hash_hmac('sha256', $encodedHeader . "." . $encodedPayload, $secret, true);
    }

    private function encodeSignatureHash(string $signature) {
        return $this->base64UrlEncode($signature);
    }

    private function buildJwt(array $payload, string $secret, ?array $header = self::DEFAULT_HEADER) {
        $encodedHeader = $this->encodeHeader($header);
        $encodedPayload = $this->encodePayload($payload);
        $signature = $this->buildSignatureHash($encodedHeader, $encodedPayload, $secret);
        $signatureHash = $this->encodeSignatureHash($signature);

        return "{$encodedHeader}.{$encodedPayload}.{$signatureHash}";
    }

    private function buildTokenData(string $jwt) {
        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) !== 3) {
            throw new JWTException(
                'JWT must consist of 3 parts separated by dots',
                400
            );
        }
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];
        return [
            self::TOKEN_DATA_HEADER => $header,
            self::TOKEN_DATA_PAYLOAD => $payload,
            self::TOKEN_DATA_SIGNATURE => $signatureProvided,
        ];
    }

    private function validateTokenData(array $tokenData) {
        foreach (array_keys($tokenData) as $key) {
            if (!in_array($key, $this->requiredTokenDataKeys)) {
                return new JWTException(
                    "{$key} should not exist in token data"
                );
            }
        }
        if (count($tokenData) === count($this->requiredTokenDataKeys)) {
            return true;
        }
        return new JWTException(
            'Token data is invalid',
            400
        );
    }

    private function validateSignature(array $tokenData, string $secret) {
        $validateTokenData = $this->validateTokenData($tokenData);
        $base64UrlHeader = $this->base64UrlEncode($tokenData[self::TOKEN_DATA_HEADER]);
        $base64UrlPayload = $this->base64UrlEncode($tokenData[self::TOKEN_DATA_PAYLOAD]);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        if ($base64UrlSignature === $tokenData[self::TOKEN_DATA_SIGNATURE]) {
            return true;
        }
        return new JWTException(
            'Signature is invalid',
            400
        );
    }

    private function buildDecodedTokenData(array $tokenData) {
        $buildData = [];
        foreach ($tokenData as $key => $value) {
            if ($key !== self::TOKEN_DATA_SIGNATURE) {
                $buildData[$key] = json_decode($value, true);
                continue;
            }
            $buildData[$key] = $value;
        }
        return $buildData;
    }

    public function jwtRawEncode(array $data)
    {
        $this->setDefaultPayload();
        return $this->buildJwt(
            $data,
            $this->getSecret()
        );
    }

    public function jwtRawDecode(string $jwt)
    {
        $tokenData = $this->buildTokenData($jwt);
        $validateSignature = $this->validateSignature($tokenData, $this->getSecret());

        return $this->buildDecodedTokenData($tokenData);
    }

    public function base64UrlEncode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    /**
     * @return array
     */
    public function getDefaultPayload(): array
    {
        return $this->defaultPayload;
    }

    /**
     * @param array $defaultPayload
     */
    public function setDefaultPayload(?array $defaultPayload = null): void
    {
        if (!$defaultPayload) {
            $this->defaultPayload = [
                self::ISSUED_AT => Carbon::now()->timestamp
            ];
            return;
        }
        $this->defaultPayload = $defaultPayload;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    public function getPayload(array $decodedToken) {
        if (!isset($decodedToken[self::TOKEN_DATA_PAYLOAD])) {
            return new JWTException(
                'Payload not found'
            );
        }
        return $decodedToken[self::TOKEN_DATA_PAYLOAD];
    }
}
