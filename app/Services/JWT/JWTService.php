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

        // Apply base64UrlDecode before base64_decode
        $header = $this->base64UrlDecode($tokenParts[0]);
        $payload = $this->base64UrlDecode($tokenParts[1]);
        $signatureProvided = $tokenParts[2]; // Signature part is already in Base64Url format

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
        // These are already decoded strings, so re-encode them to Base64Url for signature verification
        $base64UrlHeader = $this->base64UrlEncode(json_encode(json_decode($tokenData[self::TOKEN_DATA_HEADER], true)));
        $base64UrlPayload = $this->base64UrlEncode(json_encode(json_decode($tokenData[self::TOKEN_DATA_PAYLOAD], true)));

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
                // The values in $tokenData['header'] and $tokenData['payload'] are already
                // the raw decoded strings (JSON). So, just decode them from JSON.
                $buildData[$key] = json_decode($value, true);
                continue;
            }
            $buildData[$key] = $value; // Signature remains Base64Url encoded for comparison
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
        if ($validateSignature instanceof JWTException) {
            throw $validateSignature;
        }

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

    public function base64UrlDecode($text)
    {
        $data = str_replace(['-', '_'], ['+', '/'], $text);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
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
            throw new JWTException( // Changed to throw exception directly
                'Payload not found'
            );
        }
        return $decodedToken[self::TOKEN_DATA_PAYLOAD];
    }
}