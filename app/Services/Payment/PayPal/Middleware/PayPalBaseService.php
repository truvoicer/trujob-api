<?php

namespace App\Services\Payment\PayPal\Middleware;

use Exception;
use Illuminate\Support\Facades\Http; // Import the Http facade

/**
 * Class PayPalBaseService
 *
 * This service class handles authentication with the PayPal REST API,
 * manages access tokens, and provides a base for making authenticated
 * API requests. It now utilizes Laravel's Http client.
 */
class PayPalBaseService
{
    /**
     * @var string The PayPal API Client ID.
     */
    protected string $clientId;

    /**
     * @var string The PayPal API Client Secret.
     */
    protected string $clientSecret;

    /**
     * @var string The base URL for the PayPal API (e.g., sandbox or live).
     */
    protected string $baseUrl;

    /**
     * @var string|null The stored PayPal access token.
     */
    protected ?string $accessToken = null;

    /**
     * @var int The Unix timestamp when the access token expires.
     */
    protected int $tokenExpiresAt = 0;

    protected PayPalResponse $payPalResponse;

    protected array $responseData = [];

    public function __construct(
    )
    {
        // Initialize the PayPal response handler
        $this->payPalResponse = new PayPalResponse([]);
    }

    public function getResponseData(): array
    {
        return $this->responseData;
    }

    public function setResponseData(array $responseData): void
    {
        $this->responseData = $responseData;
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * Sets the PayPal API credentials.
     *
     * @param string $clientId The PayPal API Client ID.
     * @param string $clientSecret The PayPal API Client Secret.
     * @return void
     */
    public function setCredentials(string $clientId, string $clientSecret): void
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        // Invalidate current token if credentials change
        $this->accessToken = null;
        $this->tokenExpiresAt = 0;
    }

    /**
     * Sets the PayPal API environment (sandbox or live).
     *
     * @param bool $isSandbox True for sandbox, false for live.
     * @return void
     */
    public function setSandboxMode(bool $isSandbox): void
    {
        $this->baseUrl = $isSandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
        // Invalidate current token if environment changes
        $this->accessToken = null;
        $this->tokenExpiresAt = 0;
    }

    /**
     * Retrieves a valid PayPal access token, refreshing it if necessary.
     *
     * @return string The valid access token.
     * @throws Exception If authentication fails.
     */
    public function getAccessToken(): string
    {
        // If no token exists or it's expired, authenticate
        if (empty($this->accessToken) || $this->isTokenExpired()) {
            $this->authenticate();
        }

        if (empty($this->accessToken)) {
            throw new Exception('Failed to obtain PayPal access token.');
        }

        return $this->accessToken;
    }

    /**
     * Performs the OAuth2 authentication request to get an access token.
     *
     * @return void
     * @throws Exception If the request fails or returns an error.
     */
    protected function authenticate(): void
    {
        $url = $this->baseUrl . '/v1/oauth2/token';

        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                            ->asForm() // Sets Content-Type to application/x-www-form-urlencoded
                            ->post($url, [
                                'grant_type' => 'client_credentials',
                            ]);

            $responseData = $response->json();
            $this->setResponseData($responseData);

            if ($response->failed() || !isset($responseData['access_token'])) {
                $errorMessage = $responseData['error_description'] ?? $response->body() ?? 'Unknown authentication error.';
                throw new Exception("PayPal authentication failed (HTTP {$response->status()}): " . $errorMessage);
            }

            $this->accessToken = $responseData['access_token'];
            // Set expiry time a little before the actual expiry to be safe
            $this->tokenExpiresAt = time() + ($responseData['expires_in'] ?? 3600) - 60;

        } catch (\Illuminate\Http\Client\RequestException $e) {
            throw new Exception("HTTP request error during authentication: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error during PayPal authentication: " . $e->getMessage());
        }
    }

    /**
     * Checks if the current access token has expired.
     *
     * @return bool True if the token is expired or close to expiring, false otherwise.
     */
    protected function isTokenExpired(): bool
    {
        // Consider the token expired if it's within 60 seconds of its actual expiry time
        return time() >= $this->tokenExpiresAt;
    }

    /**
     * Makes an authenticated request to the PayPal API.
     *
     * @param string $method The HTTP method (GET, POST, PUT, DELETE).
     * @param string $endpoint The API endpoint (e.g., '/v2/checkout/orders').
     * @param array $data The request body data (for POST/PUT).
     * @param array $headers Additional headers to send with the request.
     * @return array The decoded JSON response from the API.
     * @throws Exception If the request fails or returns an error.
     */
    public function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = $this->baseUrl . $endpoint;
        $accessToken = $this->getAccessToken(); // Ensure we have a valid token

        // Start with the base HTTP client instance with token
        $request = Http::withToken($accessToken);

        // Merge default headers with any custom headers
        // Laravel's Http client automatically sets Content-Type: application/json for JSON bodies
        // and Authorization: Bearer for withToken().
        // We only need to add custom headers.
        if (!empty($headers)) {
            $request->withHeaders($headers);
        }

        try {
            // Dynamically call the HTTP method
            $response = match (strtoupper($method)) {
                'GET' => $request->get($url),
                'POST' => $request->post($url, $data),
                'PUT' => $request->put($url, $data),
                'DELETE' => $request->delete($url, $data),
                default => throw new Exception("Unsupported HTTP method: {$method}"),
            };

            $this->payPalResponse->setResponse($response);

            $responseData = $response->json();
            $this->setResponseData($responseData);

            // Laravel's Http client has convenient methods for checking response status
            if ($response->failed()) {
                $errorMessage = $responseData['message'] ?? $responseData['error'] ?? $response->body() ?? 'Unknown API error.';
                throw new Exception("PayPal API request to {$endpoint} failed (HTTP {$response->status()}): " . $errorMessage);
            }

            return $responseData;

        } catch (\Illuminate\Http\Client\RequestException $e) {
            // This catches exceptions for client errors (4xx) and server errors (5xx) automatically
            throw new Exception("HTTP request error during API request to {$endpoint}: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error during PayPal API request to {$endpoint}: " . $e->getMessage());
        }
    }

    protected function handleResponse(PayPalResponse $response): PayPalResponse
    {
        $response->setResponse($this->payPalResponse->getResponse());
        return $response;
    }
}
