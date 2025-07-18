<?php

namespace App\Services\Payment\PayPal\Billing;

use App\Services\Payment\PayPal\PayPalBaseService;
use Exception;

/**
 * Class PayPalBillingPlanService
 *
 * This service class handles operations related to PayPal Billing Plans,
 * extending the base PayPal service for authentication and request handling.
 *
 * PayPal Billing Plans API documentation: https://developer.paypal.com/docs/api/subscriptions/v1/#plans
 */
class PayPalBillingPlanService extends PayPalBaseService
{
    /**
     * The base endpoint for PayPal Billing Plans API.
     */
    protected const PLANS_ENDPOINT = '/v1/billing/plans';

    /**
     * Creates a new billing plan in PayPal using a PayPalBillingPlanBuilder instance.
     *
     * @param PayPalBillingPlanBuilder $builder The billing plan builder instance containing plan data.
     * @return array The created billing plan details.
     * @throws Exception If the billing plan creation fails.
     */
    public function createPlan(PayPalBillingPlanBuilder $builder): array
    {
        try {
            // Get the built billing plan data array from the builder
            $planData = $builder->get();
            $response = $this->makeRequest('POST', self::PLANS_ENDPOINT, $planData);

            if (empty($response['id'])) {
                throw new Exception('Billing plan creation failed: No plan ID returned.');
            }
            return $response;
        } catch (Exception $e) {
            throw new Exception("Failed to create PayPal billing plan: " . $e->getMessage());
        }
    }

    /**
     * Retrieves a list of billing plans from PayPal.
     *
     * @param int $pageSize The number of plans to return in the response.
     * @param int $page The page number to return.
     * @param string|null $productId Filter plans by product ID.
     * @param string|null $planStatus Filter plans by status (e.g., 'ACTIVE', 'INACTIVE').
     * @return array A list of billing plans.
     * @throws Exception If fetching plans fails.
     */
    public function listPlans(int $pageSize = 10, int $page = 1, ?string $productId = null, ?string $planStatus = null): array
    {
        $queryParams = [
            'page_size' => $pageSize,
            'page' => $page,
        ];

        if ($productId) {
            $queryParams['product_id'] = $productId;
        }
        if ($planStatus) {
            $queryParams['plan_status'] = $planStatus;
        }

        $endpoint = self::PLANS_ENDPOINT . '?' . http_build_query($queryParams);

        try {
            return $this->makeRequest('GET', $endpoint);
        } catch (Exception $e) {
            throw new Exception("Failed to list PayPal billing plans: " . $e->getMessage());
        }
    }

    /**
     * Retrieves details for a specific billing plan by its ID.
     *
     * @param string $planId The ID of the billing plan to retrieve.
     * @return array The billing plan details.
     * @throws Exception If the plan is not found or fetching fails.
     */
    public function showPlan(string $planId): array
    {
        $endpoint = self::PLANS_ENDPOINT . '/' . $planId;
        try {
            return $this->makeRequest('GET', $endpoint);
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve PayPal billing plan '{$planId}': " . $e->getMessage());
        }
    }

    /**
     * Updates an existing billing plan in PayPal.
     *
     * @param string $planId The ID of the billing plan to update.
     * @param array $patchData An array of patch objects as defined by JSON Patch (RFC 6902).
     * Example:
     * [
     * [
     * 'op' => 'replace',
     * 'path' => '/description',
     * 'value' => 'New description for the plan.'
     * ]
     * ]
     * @return array The updated plan details. Note: PayPal's PATCH typically returns a 204 No Content,
     * so you might need to fetch the plan again to get the updated details.
     * @throws Exception If the plan update fails.
     */
    public function updatePlan(string $planId, array $patchData): array
    {
        $endpoint = self::PLANS_ENDPOINT . '/' . $planId;
        try {
            $response = $this->makeRequest('PATCH', $endpoint, $patchData, ['Content-Type: application/json-patch+json']);
            return $response; // Will be empty array if 204 No Content
        } catch (Exception $e) {
            throw new Exception("Failed to update PayPal billing plan '{$planId}': " . $e->getMessage());
        }
    }

    /**
     * Activates a billing plan.
     *
     * @param string $planId The ID of the plan to activate.
     * @return bool True on success.
     * @throws Exception If activation fails.
     */
    public function activatePlan(string $planId): bool
    {
        $endpoint = self::PLANS_ENDPOINT . '/' . $planId . '/activate';
        try {
            $this->makeRequest('POST', $endpoint);
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to activate PayPal billing plan '{$planId}': " . $e->getMessage());
        }
    }

    /**
     * Deactivates a billing plan.
     *
     * @param string $planId The ID of the plan to deactivate.
     * @return bool True on success.
     * @throws Exception If deactivation fails.
     */
    public function deactivatePlan(string $planId): bool
    {
        $endpoint = self::PLANS_ENDPOINT . '/' . $planId . '/deactivate';
        try {
            $this->makeRequest('POST', $endpoint);
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to deactivate PayPal billing plan '{$planId}': " . $e->getMessage());
        }
    }

    /**
     * Deletes a billing plan.
     * Note: PayPal's API for billing plans (v1) does not directly support DELETE.
     * Plans are typically deactivated. This method is included for completeness
     * but will throw an exception as a placeholder.
     *
     * @param string $planId The ID of the plan to delete.
     * @return bool True if the plan was successfully "deleted" (or deactivated).
     * @throws Exception If direct deletion is not supported or fails.
     */
    public function deletePlan(string $planId): bool
    {
        // As of PayPal Subscriptions API v1, there is no direct DELETE endpoint for plans.
        // Plans are usually managed by deactivating them.
        throw new Exception("Direct deletion of billing plans is not supported by PayPal Subscriptions API v1. Consider deactivating the plan instead.");

        // Example of how you *would* call it if a DELETE endpoint existed:
        // $endpoint = self::PLANS_ENDPOINT . '/' . $planId;
        // try {
        //     $this->makeRequest('DELETE', $endpoint);
        //     return true;
        // } catch (Exception $e) {
        //     throw new Exception("Failed to delete PayPal billing plan '{$planId}': " . $e->getMessage());
        // }
    }
}
