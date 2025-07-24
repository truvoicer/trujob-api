<?php

namespace App\Services\Payment\PayPal\Middleware\Order;

use PaypalServerSdkLib\Http\ApiResponse;

/**
 * Class PayPalOrderResponseHandler
 *
 * Handles and parses the ApiResponse received from PayPal's Order creation.
 */
class PayPalOrderResponseHandler
{
    private ApiResponse $response;

    public function __construct(ApiResponse $response)
    {
        $this->response = $response;
    }

    public function getResult()
    {
        return $this->response->getResult();
    }

    /**
     * Checks if the PayPal order creation was successful.
     *
     * @return bool True if the order was successfully created, false otherwise.
     */
    public function isSuccess(): bool
    {
        // PayPal API successful responses typically have a 2xx status code.
        // For order creation, a 201 Created is expected.
        return $this->response->isSuccess() && $this->response->getStatusCode() === 201;
    }

    /**
     * Retrieves the PayPal Order ID from the successful response.
     *
     * @return string|null The PayPal Order ID if successful, otherwise null.
     */
    public function getOrderId(): ?string
    {
        if ($this->isSuccess()) {
            $result = $this->response->getResult();
            // Debugging line to check the structure of the result
            // Access 'id' directly as a property of the result object
            return $result->getId() ?? null;
        }
        return null;
    }

    /**
     * Retrieves the approval link (redirect URL) for the PayPal order.
     * This URL is where the user needs to be redirected to approve the payment.
     *
     * @return string|null The approval link URL if available, otherwise null.
     */
    public function getApprovalLink(): ?string
    {
        if ($this->isSuccess()) {
            $result = $this->response->getResult();
            $links = $result->getLinks();

            // Access 'links' directly as a property of the result object
            if (isset($links) && is_array($links)) {
                foreach ($links as $link) {
                    // Access 'rel' and 'href' directly as properties of each link object
                    $rel = $link->getRel();
                    $href = $link->getHref();
                    if (isset($rel) && $rel === 'approve' && isset($href)) {
                        return $href;
                    }
                }
            }
        }
        return null;
    }

    public function getLinks(): array
    {
        if ($this->isSuccess()) {
            $result = $this->response->getResult();
            $links = $result->getLinks();

            // Access 'links' directly as a property of the result object
            if (isset($links) && is_array($links)) {
                return array_map(function ($link) {
                    return [
                        'rel' => $link->getRel(),
                        'href' => $link->getHref(),
                        'method' => $link->getMethod(),
                    ];
                }, $links);
            }
        }
        return [];
    }



    /**
     * Retrieves the raw error message from the API response if not successful.
     *
     * @return string|null The error message, or null if no error or response is successful.
     */
    public function getErrorMessage(): ?string
    {
        if (!$this->isSuccess()) {
            $result = $this->response->getResult();
            // Attempt to get message from result object, fallback to raw body
            return (is_object($result) && isset($result->message)) ? $result->message : $this->response->getBody();
        }
        return null;
    }

    /**
     * Retrieves detailed error information from the API response if not successful.
     *
     * @return array The error details, or an empty array if no details or response is successful.
     */
    public function getErrorDetails(): array
    {
        if (!$this->isSuccess()) {
            $result = $this->response->getResult();
            return (is_object($result) && isset($result->details) && is_array($result->details)) ? $result->details : [];
        }
        return [];
    }

    /**
     * Returns the raw API response object.
     *
     * @return ApiResponse
     */
    public function getRawResponse(): ApiResponse
    {
        return $this->response;
    }
}
