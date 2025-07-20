<?php

namespace App\Services\Stripe;

use App\Services\Payment\Stripe\Middleware\StripeBaseService;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

/**
 * CheckoutSessionService Class
 *
 * This class extends the StripeBaseService and provides methods specifically for
 * creating and managing Stripe Checkout Sessions. It encapsulates the logic
 * for setting up various types of checkout experiences, such as one-time payments
 * and subscriptions.
 */
class CheckoutSessionService extends StripeBaseService
{
    /**
     * Creates a new Stripe Checkout Session for a one-time payment.
     *
     * @param array $lineItems An array of line item objects for the products being purchased.
     * Each item should typically have 'price_data' or 'price' and 'quantity'.
     * Example: [[ 'price_data' => ['currency' => 'usd', 'product_data' => ['name' => 'Product Name'], 'unit_amount' => 2000], 'quantity' => 1 ]]
     * @param string $successUrl The URL to redirect to after successful checkout.
     * @param string $cancelUrl The URL to redirect to if the user cancels checkout.
     * @param array $options Additional options for the session (e.g., customer_email, metadata).
     * @return \Stripe\Checkout\Session The created Checkout Session object.
     * @throws \Exception If a Stripe API error occurs.
     */
    public function createOneTimePaymentSession(array $lineItems, string $successUrl, string $cancelUrl, array $options = []): Session
    {
        $params = array_merge([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ], $options);

        Log::info('Attempting to create one-time payment checkout session.', $params);

        return $this->callStripeApi(function () use ($params) {
            return Session::create($params);
        });
    }

    /**
     * Creates a new Stripe Checkout Session for a subscription.
     *
     * @param string $priceId The ID of the Stripe Price object for the subscription.
     * @param string $successUrl The URL to redirect to after successful checkout.
     * @param string $cancelUrl The URL to redirect to if the user cancels checkout.
     * @param string|null $customerId Optional: The ID of an existing Stripe Customer.
     * @param array $options Additional options for the session (e.g., metadata, trial_end).
     * @return \Stripe\Checkout\Session The created Checkout Session object.
     * @throws \Exception If a Stripe API error occurs.
     */
    public function createSubscriptionSession(string $priceId, string $successUrl, string $cancelUrl, ?string $customerId = null, array $options = []): Session
    {
        $lineItems = [
            [
                'price' => $priceId,
                'quantity' => 1,
            ],
        ];

        $params = array_merge([
            'mode' => 'subscription',
            'line_items' => $lineItems,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ], $options);

        // If a customer ID is provided, attach the session to that customer.
        if ($customerId) {
            $params['customer'] = $customerId;
        }

        Log::info('Attempting to create subscription checkout session.', $params);

        return $this->callStripeApi(function () use ($params) {
            return Session::create($params);
        });
    }

    /**
     * Retrieves a Stripe Checkout Session by its ID.
     *
     * @param string $sessionId The ID of the Checkout Session to retrieve.
     * @param array $options Additional options for retrieval (e.g., expand).
     * @return \Stripe\Checkout\Session The retrieved Checkout Session object.
     * @throws \Exception If a Stripe API error occurs.
     */
    public function retrieveSession(string $sessionId, array $options = []): Session
    {
        Log::info('Attempting to retrieve checkout session.', ['session_id' => $sessionId]);

        return $this->callStripeApi(function () use ($sessionId, $options) {
            return Session::retrieve($sessionId, $options);
        });
    }
}
