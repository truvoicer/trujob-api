<?php

namespace App\Services\Payment\Stripe\Middleware;

use Stripe\Stripe;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

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

    protected StripeClient $stripeClient;

    /**
     * The Stripe API secret key.
     *
     * @var string
     */
    protected $apiKey;

    protected string $currencyCode;
    protected string $locale;


    protected StripeResponse $stripeResponse;

    /**
     * Constructor for the StripeBaseService.
     * Initializes Stripe with the API key and version from configuration.
     */
    public function __construct()
    {
        $this->stripeResponse = new StripeResponse([]);
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getClient(): StripeClient
    {
        return $this->stripeClient;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Initializes the Stripe client with the API key.
     *
     * @throws \Exception If the API key is not set.
     */
    protected function initializeStripeClient(): void
    {
        if (!$this->apiKey) {
            throw new \Exception('Stripe API key is not set.');
        }
        $this->stripeClient = new StripeClient($this->apiKey);

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
            $this->initializeStripeClient();
            return $callback();
        } catch (ApiErrorException $e) {
            // Log the Stripe API error for debugging.
            Log::error('Stripe API Error: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'http_status' => $e->getHttpStatus(),
                'stripe_code' => $e->getStripeCode(),
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

}
