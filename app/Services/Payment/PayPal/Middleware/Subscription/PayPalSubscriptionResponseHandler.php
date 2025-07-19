<?php

namespace App\Services\Payment\PayPal\Middleware\Subscription;

use App\Services\Payment\PayPal\Middleware\PayPalResponse;

/**
 * Class PayPalSubscriptionBuilder
 *
 * A fluent builder for constructing PayPal subscription data arrays.
 */
class PayPalSubscriptionResponseHandler extends PayPalResponse
{


    /**
     * Retrieves the PayPal Order ID from the successful response.
     *
     * @return string|null The PayPal Subscription ID if successful, otherwise null.
     */
    public function getSubscriptionId(): ?string
    {
        if ($this->isSuccess()) {
            $result = $this->getResponseData();
            // Debugging line to check the structure of the result
            // Access 'id' directly as a property of the result object
            return $result['id'] ?? null;
        }
        return null;
    }


}
