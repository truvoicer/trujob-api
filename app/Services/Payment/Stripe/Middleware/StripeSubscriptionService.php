<?php

namespace App\Services\Payment\Stripe\Middleware;

use Stripe\Subscription;
use Illuminate\Support\Facades\Log;

/**
 * StripeSubscriptionService Class
 *
 * This class extends the BaseService and provides methods for performing
 * CRUD (Create, Retrieve, Update, Delete) operations on Stripe Subscriptions.
 * It encapsulates the logic for interacting with the Stripe Subscription API.
 */
class StripeSubscriptionService extends StripeBaseService
{
    /**
     * Creates a new Stripe Subscription.
     *
     * @param string $customerId The ID of the customer to subscribe.
     * @param array $items An array of subscription item objects. Each item should have 'price' (ID) and 'quantity'.
     * Example: [['price' => 'price_123', 'quantity' => 1]]
     * @param array $options Additional options for the subscription (e.g., trial_end, default_payment_method, metadata).
     * @return \Stripe\Subscription The created Subscription object.
     * @throws \Exception If a Stripe API error occurs.
     */
    public function createSubscription(string $customerId, array $items, array $options = []): Subscription
    {
        $params = array_merge([
            'customer' => $customerId,
            'items' => $items,
        ], $options);

        Log::info('Attempting to create Stripe Subscription.', $params);

        return $this->callStripeApi(function () use ($params) {
            return Subscription::create($params);
        });
    }

    /**
     * Retrieves a Stripe Subscription by its ID.
     *
     * @param string $subscriptionId The ID of the Subscription to retrieve.
     * @param array $options Additional options for retrieval (e.g., expand).
     * @return \Stripe\Subscription The retrieved Subscription object.
     * @throws \Exception If a Stripe API error occurs.
     */
    public function retrieveSubscription(string $subscriptionId, array $options = []): Subscription
    {
        Log::info('Attempting to retrieve Stripe Subscription.', ['subscription_id' => $subscriptionId]);

        return $this->callStripeApi(function () use ($subscriptionId, $options) {
            return Subscription::retrieve($subscriptionId, $options);
        });
    }

    /**
     * Updates an existing Stripe Subscription.
     *
     * @param string $subscriptionId The ID of the Subscription to update.
     * @param array $updates An associative array of parameters to update (e.g., 'cancel_at_period_end' => true, 'items' => [...]).
     * @return \Stripe\Subscription The updated Subscription object.
     * @throws \Exception If a Stripe API error occurs.
     */
    public function updateSubscription(string $subscriptionId, array $updates): Subscription
    {
        Log::info('Attempting to update Stripe Subscription.', ['subscription_id' => $subscriptionId, 'updates' => $updates]);

        return $this->callStripeApi(function () use ($subscriptionId, $updates) {
            $subscription = Subscription::retrieve($subscriptionId);
            return $subscription->update($updates);
        });
    }

    /**
     * Cancels a Stripe Subscription.
     *
     * @param string $subscriptionId The ID of the Subscription to cancel.
     * @param array $options Additional options for cancellation (e.g., 'at_period_end' => true).
     * @return \Stripe\Subscription The canceled Subscription object.
     * @throws \Exception If a Stripe API error occurs.
     */
    public function cancelSubscription(string $subscriptionId, array $options = []): Subscription
    {
        Log::info('Attempting to cancel Stripe Subscription.', ['subscription_id' => $subscriptionId, 'options' => $options]);

        return $this->callStripeApi(function () use ($subscriptionId, $options) {
            $subscription = Subscription::retrieve($subscriptionId);
            return $subscription->cancel($options);
        });
    }

    /**
     * Deletes a Stripe Subscription immediately.
     * Note: This is a hard delete and cannot be undone. Use cancelSubscription for softer cancellation.
     *
     * @param string $subscriptionId The ID of the Subscription to delete.
     * @return \Stripe\Subscription The deleted Subscription object.
     * @throws \Exception If a Stripe API error occurs.
     */
    public function deleteSubscription(string $subscriptionId): Subscription
    {
        Log::warning('Attempting to delete Stripe Subscription permanently.', ['subscription_id' => $subscriptionId]);

        return $this->callStripeApi(function () use ($subscriptionId) {
            $subscription = Subscription::retrieve($subscriptionId);
            return $subscription->delete();
        });
    }
}
