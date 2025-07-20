<?php

namespace App\Services\Payment\Stripe\Middleware;

use Stripe\Stripe;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * StripeBaseService Class
 *
 * This class serves as a foundational service for interacting with the Stripe API.
 * It handles common functionalities such as:
 * - Setting Stripe API key and version.
 * - Retrieving and storing access tokens (though Stripe PHP library primarily uses API keys,
 * this can be extended for OAuth scenarios if needed).
 * - Handling API responses and exceptions.
 * - Providing a structured way to interact with the Stripe SDK.
 */
class StripeBaseService
{
    /**
     * The Stripe API secret key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The Stripe API version.
     *
     * @var string
     */
    protected $apiVersion;

    /**
     * Constructor for the StripeBaseService.
     * Initializes Stripe with the API key and version from configuration.
     */
    public function __construct()
    {
        // Retrieve Stripe API key from Laravel configuration.
        // Ensure you have 'stripe.secret_key' and 'stripe.api_version'
        // defined in your config/services.php or a dedicated config file.
        $this->apiKey = config('services.stripe.secret_key');
        $this->apiVersion = config('services.stripe.api_version', '2024-06-20'); // Use a default version if not set

        // Set the Stripe API key globally for the Stripe PHP library.
        Stripe::setApiKey($this->apiKey);
        Stripe::setApiVersion($this->apiVersion);
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
        Stripe::setApiKey($this->apiKey);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }


    /**
     * Retrieves an access token.
     *
     * While the Stripe PHP library primarily uses a persistent API key,
     * this method is a placeholder for scenarios requiring dynamic token management
     * (e.g., Stripe Connect OAuth where you get access tokens for connected accounts).
     * For standard API key usage, this might simply return the configured API key
     * or null if not applicable.
     *
     * @return string|null The access token, or null if not applicable.
     */
    protected function getAccessToken(): ?string
    {
        // Example: If using OAuth, you might retrieve the token from cache or database.
        // For direct API key usage, this method might not be strictly necessary
        // as the API key is set globally in the constructor.
        // return Cache::get('stripe_access_token');
        return $this->apiKey; // For basic API key usage
    }

    /**
     * Stores an access token.
     *
     * Similar to getAccessToken, this is primarily for OAuth flows.
     * For standard API key usage, this method might not be used.
     *
     * @param string $token The access token to store.
     * @param int $expiresIn The expiration time in seconds.
     * @return void
     */
    protected function storeAccessToken(string $token, int $expiresIn = 3600): void
    {
        // Example: Store the token in cache with an expiration.
        // Cache::put('stripe_access_token', $token, $expiresIn);
        Log::info('Stripe access token stored (if applicable)', ['token_length' => strlen($token), 'expires_in' => $expiresIn]);
    }

    /**
     * Handles API responses and exceptions.
     *
     * This method wraps Stripe API calls in a try-catch block to gracefully handle
     * errors and log them. It returns the result of the API call or throws
     * a custom exception/returns null on error.
     *
     * @param callable $callback The Stripe API call to execute.
     * @return mixed The result of the Stripe API call.
     * @throws \Exception If a Stripe API error occurs.
     */
    protected function callStripeApi(callable $callback)
    {
        try {
            return $callback();
        } catch (ApiErrorException $e) {
            // Log the Stripe API error for debugging.
            Log::error('Stripe API Error: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'http_status' => $e->getHttpStatus(),
                'stripe_code' => $e->getStripeCode(),
                'param' => $e->getParam(),
                'request_id' => $e->getRequestId(),
                'json_body' => $e->getJsonBody(),
            ]);

            // Re-throw the exception or return a specific error response.
            // For now, we'll re-throw to allow higher-level error handling.
            throw new \Exception('Stripe API Error: ' . $e->getMessage(), $e->getHttpStatus(), $e);
        } catch (\Exception $e) {
            // Catch any other general exceptions.
            Log::error('General Error during Stripe API call: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw new \Exception('An unexpected error occurred: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Example method to demonstrate using callStripeApi.
     * This method is not part of the core base service but shows how to use it.
     *
     * @return \Stripe\Balance|null
     */
    public function getBalance()
    {
        return $this->callStripeApi(function () {
            return \Stripe\Balance::retrieve();
        });
    }
}
