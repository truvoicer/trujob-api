<?php

namespace App\Services\Payment\PayPal\Middleware\Product;

use App\Services\Payment\PayPal\Middleware\PayPalBaseService;
use Exception;

/**
 * Class PayPalProductService
 *
 * This service class handles operations related to PayPal Products,
 * extending the base PayPal service for authentication and request handling.
 *
 * PayPal Products API documentation: https://developer.paypal.com/docs/api/catalog-products/v1/
 */
class PayPalProductService extends PayPalBaseService
{
    /**
     * The base endpoint for PayPal Products API.
     */
    protected const PRODUCTS_ENDPOINT = '/v1/catalogs/products';

    /**
     * Creates a new product in PayPal using a PayPalProductBuilder instance.
     *
     * @param PayPalProductBuilder $builder The product builder instance containing product data.
     * @return array The created product details.
     * @throws Exception If the product creation fails.
     */
    public function createProduct(PayPalProductBuilder $builder): array
    {
        try {
            // Get the built product data array from the builder
            $productData = $builder->get();
            $response = $this->makeRequest('POST', self::PRODUCTS_ENDPOINT, $productData);
            if (empty($response['id'])) {
                throw new Exception('Product creation failed: No product ID returned.');
            }
            return $response;
        } catch (Exception $e) {
            throw new Exception("Failed to create PayPal product: " . $e->getMessage());
        }
    }

    /**
     * Retrieves a list of products from PayPal.
     *
     * @param int $pageSize The number of products to return in the response.
     * @param int $page The page number to return.
     * @return array A list of products.
     * @throws Exception If fetching products fails.
     */
    public function listProducts(int $pageSize = 10, int $page = 1): array
    {
        $endpoint = self::PRODUCTS_ENDPOINT . "?page_size={$pageSize}&page={$page}";
        try {
            return $this->makeRequest('GET', $endpoint);
        } catch (Exception $e) {
            throw new Exception("Failed to list PayPal products: " . $e->getMessage());
        }
    }

    /**
     * Retrieves details for a specific product by its ID.
     *
     * @param string $productId The ID of the product to retrieve.
     * @return array The product details.
     * @throws Exception If the product is not found or fetching fails.
     */
    public function showProduct(string $productId): array
    {
        $endpoint = self::PRODUCTS_ENDPOINT . '/' . $productId;
        try {
            return $this->makeRequest('GET', $endpoint);
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve PayPal product '{$productId}': " . $e->getMessage());
        }
    }

    /**
     * Updates an existing product in PayPal.
     *
     * @param string $productId The ID of the product to update.
     * @param array $patchData An array of patch objects as defined by JSON Patch (RFC 6902).
     * Example:
     * [
     * [
     * 'op' => 'replace',
     * 'path' => '/description',
     * 'value' => 'New description for the product.'
     * ],
     * [
     * 'op' => 'add',
     * 'path' => '/image_url',
     * 'value' => 'https://example.com/new_image.jpg'
     * ]
     * ]
     * @return array The updated product details. Note: PayPal's PATCH typically returns a 204 No Content,
     * so you might need to fetch the product again to get the updated details.
     * @throws Exception If the product update fails.
     */
    public function updateProduct(string $productId, array $patchData): array
    {
        $endpoint = self::PRODUCTS_ENDPOINT . '/' . $productId;
        // For PATCH requests, Content-Type should be 'application/json-patch+json'
        // However, Laravel's Http client will send application/json by default for array data.
        // PayPal's API often accepts application/json for PATCH if the body is a JSON Patch array.
        try {
            // PayPal PATCH typically returns 204 No Content, so we might not get a body back.
            // We'll return an empty array if no content, or the response JSON if available.
            $response = $this->makeRequest('PATCH', $endpoint, $patchData, ['Content-Type: application/json-patch+json']);
            return $response; // Will be empty array if 204 No Content
        } catch (Exception $e) {
            throw new Exception("Failed to update PayPal product '{$productId}': " . $e->getMessage());
        }
    }

    /**
     * Deletes a product from PayPal.
     * Note: PayPal's Catalog Products API v1 does not directly support DELETE.
     * Products are typically archived or marked as inactive rather than truly deleted.
     * This method is included for completeness but might require a different approach
     * (e.g., updating a status) depending on PayPal's actual API capabilities for product removal.
     * For now, this will throw an exception as a placeholder.
     *
     * @param string $productId The ID of the product to delete.
     * @return bool True if the product was successfully "deleted" (or archived/marked inactive).
     * @throws Exception If the product deletion is not supported or fails.
     */
    public function deleteProduct(string $productId): bool
    {
        // As of PayPal Catalog Products API v1, there is no direct DELETE endpoint for products.
        // Products are usually managed by updating their status (e.g., 'INACTIVE').
        // Implement logic here if PayPal introduces a direct delete or if you manage status.
        throw new Exception("Direct deletion of products is not supported by PayPal Catalog Products API v1. Consider updating product status to 'INACTIVE' if applicable.");

        // Example of how you *would* call it if a DELETE endpoint existed:
        // $endpoint = self::PRODUCTS_ENDPOINT . '/' . $productId;
        // try {
        //     $this->makeRequest('DELETE', $endpoint);
        //     return true;
        // } catch (Exception $e) {
        //     throw new Exception("Failed to delete PayPal product '{$productId}': " . $e->getMessage());
        // }
    }
}
