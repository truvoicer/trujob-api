<?php
namespace App\Services\Payment\Stripe\Middleware;

use Illuminate\Http\Client\Response;

class StripeResponse
{
    private Response $response;


    protected array $responseData;

    public function __construct(array $responseData)
    {
        $this->responseData = $responseData;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getResponseData(): array
    {
        return $this->responseData;
    }

    public function setResponseData(array $responseData): void
    {
        $this->responseData = $responseData;
    }


    /**
     * Checks if the PayPal order creation was successful.
     *
     * @return bool True if the order was successfully created, false otherwise.
     */
    public function isSuccess(): bool
    {
        if (!isset($this->response)) {
            return false;
        }

        return $this->response->successful();
    }

    public function isFailed(): bool
    {
        return !$this->isSuccess();
    }

    /**
     * Retrieves the raw error message from the API response if not successful.
     *
     * @return string|null The error message, or null if no error or response is successful.
     */
    public function getErrorMessage(): ?string
    {
        if (!$this->isSuccess()) {
            $result = $this->getResponse();
            // Attempt to get message from result object, fallback to raw body

        }
        return null;
    }

    /**§
     * Retrieves detailed error information from the API response if not successful.
     *
     * @return array The error details, or an empty array if no details or response is successful.
     */
    public function getErrorDetails(): array
    {
        if (!$this->isSuccess()) {
            return [];
        }
        return [];
    }
}
