<?php

namespace App\Services\Payment\PayPal\Subscription;

use App\Services\Payment\PayPal\PayPalBaseService;
use Exception;

/**
 * Class PayPalSubscriptionService
 *
 * This service class handles operations related to PayPal Subscriptions,
 * extending the base PayPal service for authentication and request handling.
 *
 * PayPal Subscriptions API documentation: https://developer.paypal.com/docs/api/subscriptions/v1/
 */
class PayPalSubscriptionService extends PayPalBaseService
{
    /**
     * The base endpoint for PayPal Subscriptions API.
     */
    protected const SUBSCRIPTIONS_ENDPOINT = '/v1/billing/subscriptions';

    /**
     * Creates a new subscription in PayPal using a PayPalSubscriptionBuilder instance.
     *
     * @param PayPalSubscriptionBuilder $builder The subscription builder instance containing subscription data.
     * @return array The created subscription details.
     * @throws Exception If the subscription creation fails.
     */
    public function createSubscription(PayPalSubscriptionBuilder $builder): array
    {
        try {
            $subscriptionData = $builder->get();
            return $this->makeRequest('POST', self::SUBSCRIPTIONS_ENDPOINT, $subscriptionData);
        } catch (Exception $e) {
            throw new Exception("Failed to create PayPal subscription: " . $e->getMessage());
        }
    }

    /**
     * Retrieves a list of subscriptions from PayPal.
     *
     * @param int $pageSize The number of subscriptions to return in the response.
     * @param int $page The page number to return.
     * @param string|null $planId Filter subscriptions by plan ID.
     * @param string|null $status Filter subscriptions by status (e.g., 'APPROVAL_PENDING', 'ACTIVE', 'SUSPENDED', 'CANCELLED', 'EXPIRED').
     * @return array A list of subscriptions.
     * @throws Exception If fetching subscriptions fails.
     */
    public function listSubscriptions(int $pageSize = 10, int $page = 1, ?string $planId = null, ?string $status = null): array
    {
        $queryParams = [
            'page_size' => $pageSize,
            'page' => $page,
        ];

        if ($planId) {
            $queryParams['plan_id'] = $planId;
        }
        if ($status) {
            $queryParams['status'] = $status;
        }

        $endpoint = self::SUBSCRIPTIONS_ENDPOINT . '?' . http_build_query($queryParams);

        try {
            return $this->makeRequest('GET', $endpoint);
        } catch (Exception $e) {
            throw new Exception("Failed to list PayPal subscriptions: " . $e->getMessage());
        }
    }

    /**
     * Retrieves details for a specific subscription by its ID.
     *
     * @param string $subscriptionId The ID of the subscription to retrieve.
     * @return array The subscription details.
     * @throws Exception If the subscription is not found or fetching fails.
     */
    public function showSubscription(string $subscriptionId): array
    {
        $endpoint = self::SUBSCRIPTIONS_ENDPOINT . '/' . $subscriptionId;
        try {
            return $this->makeRequest('GET', $endpoint);
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve PayPal subscription '{$subscriptionId}': " . $e->getMessage());
        }
    }

    /**
     * Updates an existing subscription in PayPal.
     *
     * @param string $subscriptionId The ID of the subscription to update.
     * @param array $patchData An array of patch objects as defined by JSON Patch (RFC 6902).
     * Example:
     * [
     * [
     * 'op' => 'replace',
     * 'path' => '/shipping_amount/value',
     * 'value' => '15.00'
     * ]
     * ]
     * @return array The updated subscription details. Note: PayPal's PATCH typically returns a 204 No Content,
     * so you might need to fetch the subscription again to get the updated details.
     * @throws Exception If the subscription update fails.
     */
    public function updateSubscription(string $subscriptionId, array $patchData): array
    {
        $endpoint = self::SUBSCRIPTIONS_ENDPOINT . '/' . $subscriptionId;
        try {
            $response = $this->makeRequest('PATCH', $endpoint, $patchData, ['Content-Type: application/json-patch+json']);
            return $response; // Will be empty array if 204 No Content
        } catch (Exception $e) {
            throw new Exception("Failed to update PayPal subscription '{$subscriptionId}': " . $e->getMessage());
        }
    }

    /**
     * Revises the plan or quantity of an existing subscription.
     *
     * @param string $subscriptionId The ID of the subscription to revise.
     * @param string $planId The new plan ID.
     * @param int|null $quantity The new quantity (optional).
     * @param array $shippingAmount Optional new shipping amount (currency_code, value).
     * @return array The revised subscription details.
     * @throws Exception If the revision fails.
     */
    public function reviseSubscription(string $subscriptionId, string $planId, ?int $quantity = null, array $shippingAmount = []): array
    {
        $endpoint = self::SUBSCRIPTIONS_ENDPOINT . '/' . $subscriptionId . '/revise';
        $reviseData = [
            'plan_id' => $planId,
        ];

        if ($quantity !== null) {
            $reviseData['quantity'] = $quantity;
        }
        if (!empty($shippingAmount)) {
            $reviseData['shipping_amount'] = $shippingAmount;
        }

        try {
            return $this->makeRequest('POST', $endpoint, $reviseData);
        } catch (Exception $e) {
            throw new Exception("Failed to revise PayPal subscription '{$subscriptionId}': " . $e->getMessage());
        }
    }

    /**
     * Suspends a subscription.
     *
     * @param string $subscriptionId The ID of the subscription to suspend.
     * @param string|null $reason The reason for suspension (optional).
     * @return bool True on success.
     * @throws Exception If suspension fails.
     */
    public function suspendSubscription(string $subscriptionId, ?string $reason = null): bool
    {
        $endpoint = self::SUBSCRIPTIONS_ENDPOINT . '/' . $subscriptionId . '/suspend';
        $data = $reason ? ['reason' => $reason] : [];
        try {
            $this->makeRequest('POST', $endpoint, $data);
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to suspend PayPal subscription '{$subscriptionId}': " . $e->getMessage());
        }
    }

    /**
     * Cancels a subscription.
     *
     * @param string $subscriptionId The ID of the subscription to cancel.
     * @param string|null $reason The reason for cancellation (optional).
     * @return bool True on success.
     * @throws Exception If cancellation fails.
     */
    public function cancelSubscription(string $subscriptionId, ?string $reason = null): bool
    {
        $endpoint = self::SUBSCRIPTIONS_ENDPOINT . '/' . $subscriptionId . '/cancel';
        $data = $reason ? ['reason' => $reason] : [];
        try {
            $this->makeRequest('POST', $endpoint, $data);
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to cancel PayPal subscription '{$subscriptionId}': " . $e->getMessage());
        }
    }

    /**
     * Activates a suspended subscription.
     *
     * @param string $subscriptionId The ID of the subscription to activate.
     * @param string|null $reason The reason for activation (optional).
     * @return bool True on success.
     * @throws Exception If activation fails.
     */
    public function activateSubscription(string $subscriptionId, ?string $reason = null): bool
    {
        $endpoint = self::SUBSCRIPTIONS_ENDPOINT . '/' . $subscriptionId . '/activate';
        $data = $reason ? ['reason' => $reason] : [];
        try {
            $this->makeRequest('POST', $endpoint, $data);
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to activate PayPal subscription '{$subscriptionId}': " . $e->getMessage());
        }
    }

    /**
     * Captures an authorized payment on a subscription.
     * This is typically used for subscriptions with manual approval or initial payment capture.
     *
     * @param string $subscriptionId The ID of the subscription.
     * @param string $currencyCode The currency code (e.g., 'USD').
     * @param string $value The amount to capture.
     * @param bool $noteToPayer Optional note to the payer.
     * @return array The captured payment details.
     * @throws Exception If payment capture fails.
     */
    public function capturePayment(string $subscriptionId, string $currencyCode, string $value, ?string $noteToPayer = null): array
    {
        $endpoint = self::SUBSCRIPTIONS_ENDPOINT . '/' . $subscriptionId . '/capture';
        $data = [
            'amount' => [
                'currency_code' => $currencyCode,
                'value' => $value,
            ],
        ];
        if ($noteToPayer) {
            $data['note_to_payer'] = $noteToPayer;
        }

        try {
            return $this->makeRequest('POST', $endpoint, $data);
        } catch (Exception $e) {
            throw new Exception("Failed to capture payment for subscription '{$subscriptionId}': " . $e->getMessage());
        }
    }

    /**
     * Lists transactions for a specific subscription within a given time range.
     *
     * @param string $subscriptionId The ID of the subscription.
     * @param string $startTime The start date and time for the transaction range in ISO 8601 format (e.g., "2023-01-01T00:00:00Z").
     * @param string $endTime The end date and time for the transaction range in ISO 8601 format (e.g., "2023-01-31T23:59:59Z").
     * @return array A list of transactions.
     * @throws Exception If fetching transactions fails.
     */
    public function listTransactions(string $subscriptionId, string $startTime, string $endTime): array
    {
        $endpoint = self::SUBSCRIPTIONS_ENDPOINT . '/' . $subscriptionId . '/transactions';
        $queryParams = [
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
        $endpoint .= '?' . http_build_query($queryParams);

        try {
            return $this->makeRequest('GET', $endpoint);
        } catch (Exception $e) {
            throw new Exception("Failed to list transactions for subscription '{$subscriptionId}': " . $e->getMessage());
        }
    }
}
