<?php

namespace App\Services\Payment\Stripe\Middleware\Checkout;

use App\Services\Payment\Stripe\Middleware\StripeBaseService;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

/**
 * CheckoutSessionService Class
 *
 * This class extends the StripeBaseService and provides methods specifically for
 * creating and managing Stripe Checkout Sessions. It encapsulates the logic
 * for setting up various types of checkout experiences, such as one-time payments
 * and subscriptions.
 */
class StripeCheckoutService extends StripeBaseService
{
    /**
     * Creates a new Stripe Checkout Session for a one-time payment using a CheckoutSessionBuilder.
     *
     * @param \App\Services\Stripe\CheckoutSessionBuilder $builder An instance of the CheckoutSessionBuilder
     * pre-configured with session parameters.
     * The builder's mode should be 'payment'.
     * @return \Stripe\Checkout\Session The created Checkout Session object.
     * @throws \Exception If a Stripe API error occurs or builder validation fails.
     */
    public function createOneTimePaymentSession(StripeCheckoutSessionBuilder $builder): Session
    {
        // Ensure the builder is configured for payment mode, if not already.
        // The builder's build() method will also perform validation.
        $builder->setMode('payment');
        $params = $builder->build();

        return $this->callStripeApi(function () use ($params) {
            return $this->stripeClient->checkout->sessions->create($params);
        });
    }

    /**
     * Creates a new Stripe Checkout Session for a subscription using a CheckoutSessionBuilder.
     *
     * @param \App\Services\Stripe\CheckoutSessionBuilder $builder An instance of the CheckoutSessionBuilder
     * pre-configured with session parameters.
     * The builder's mode should be 'subscription'.
     * @return \Stripe\Checkout\Session The created Checkout Session object.
     * @throws \Exception If a Stripe API error occurs or builder validation fails.
     */
    public function createSubscriptionSession(StripeCheckoutSessionBuilder $builder): Session
    {
        // Ensure the builder is configured for subscription mode, if not already.
        // The builder's build() method will also perform validation.
        $builder->setMode('subscription');
        $params = $builder->build();

        Log::info('Attempting to create subscription checkout session using builder.', $params);


        return $this->callStripeApi(function () use ($params) {
            return $this->stripeClient->checkout->sessions->create($params);
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
            return $this->stripeClient->checkout->sessions->retrieve($sessionId, $options);
        });
    }
}
