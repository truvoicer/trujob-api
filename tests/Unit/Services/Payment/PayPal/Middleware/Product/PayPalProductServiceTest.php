<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware\Product;

use App\Services\Payment\PayPal\Middleware\Product\PayPalProductService;
use App\Services\Payment\PayPal\Middleware\Product\PayPalProductBuilder;
use Exception;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayPalProductServiceTest extends TestCase
{
    private PayPalProductService $payPalProductService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the PayPalBaseService dependencies if needed, e.g., the access token.
        // For this example, we'll instantiate directly, but in a real application,
        // you'd likely mock the authentication service or client.
        $this->payPalProductService = new PayPalProductService();
    }

    public function testCreateProductSuccess(): void
    {
        // Arrange
        $productData = [
            'name' => 'Test Product',
            'description' => 'This is a test product.',
            'type' => 'SERVICE',
            'category' => 'SOFTWARE',
        ];

        $mockResponse = [
            'id' => 'PRODUCT-123',
            'name' => 'Test Product',
            'description' => 'This is a test product.',
            'type' => 'SERVICE',
            'category' => 'SOFTWARE',
        ];

        Http::fake([
            '*/v1/catalogs/products' => Http::response($mockResponse, 201),
        ]);

        $builder = new PayPalProductBuilder($productData['name'], $productData['type']);
        $builder->description($productData['description']);
        $builder->category($productData['category']);


        // Act
        $result = $this->payPalProductService->createProduct($builder);

        // Assert
        $this->assertEquals('PRODUCT-123', $result['id']);
        $this->assertEquals('Test Product', $result['name']);
    }

    public function testCreateProductFailure(): void
    {
        // Arrange
        $productData = [
            'name' => 'Test Product',
            'description' => 'This is a test product.',
            'type' => 'SERVICE',
            'category' => 'SOFTWARE',
        ];

        Http::fake([
            '*/v1/catalogs/products' => Http::response(['message' => 'Product creation failed'], 400),
        ]);

        $builder = new PayPalProductBuilder($productData['name'], $productData['type']);
        $builder->description($productData['description']);
        $builder->category($productData['category']);


        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to create PayPal product: Product creation failed");

        // Act
        $this->payPalProductService->createProduct($builder);
    }

    public function testListProductsSuccess(): void
    {
        // Arrange
        $mockResponse = [
            'products' => [
                ['id' => 'PRODUCT-123', 'name' => 'Product 1'],
                ['id' => 'PRODUCT-456', 'name' => 'Product 2'],
            ],
            'total_items' => 2,
            'total_pages' => 1,
        ];

        Http::fake([
            '*/v1/catalogs/products?page_size=10&page=1' => Http::response($mockResponse, 200),
        ]);

        // Act
        $result = $this->payPalProductService->listProducts();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('products', $result);
        $this->assertCount(2, $result['products']);
        $this->assertEquals('PRODUCT-123', $result['products'][0]['id']);
        $this->assertEquals('Product 1', $result['products'][0]['name']);
    }

    public function testListProductsFailure(): void
    {
        // Arrange
        Http::fake([
            '*/v1/catalogs/products?page_size=10&page=1' => Http::response(['message' => 'Failed to fetch products'], 500),
        ]);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to list PayPal products: Failed to fetch products");

        // Act
        $this->payPalProductService->listProducts();
    }

    public function testShowProductSuccess(): void
    {
        // Arrange
        $productId = 'PRODUCT-123';
        $mockResponse = [
            'id' => $productId,
            'name' => 'Test Product',
        ];

        Http::fake([
            "*/v1/catalogs/products/{$productId}" => Http::response($mockResponse, 200),
        ]);

        // Act
        $result = $this->payPalProductService->showProduct($productId);

        // Assert
        $this->assertEquals($productId, $result['id']);
        $this->assertEquals('Test Product', $result['name']);
    }

    public function testShowProductFailure(): void
    {
        // Arrange
        $productId = 'PRODUCT-123';
        Http::fake([
            "*/v1/catalogs/products/{$productId}" => Http::response(['message' => 'Product not found'], 404),
        ]);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to retrieve PayPal product '{$productId}': Product not found");

        // Act
        $this->payPalProductService->showProduct($productId);
    }

    public function testUpdateProductSuccess(): void
    {
        // Arrange
        $productId = 'PRODUCT-123';
        $patchData = [
            [
                'op' => 'replace',
                'path' => '/description',
                'value' => 'New description',
            ],
        ];

        Http::fake([
            "*/v1/catalogs/products/{$productId}" => Http::response([], 204),
        ]);

        // Act
        $result = $this->payPalProductService->updateProduct($productId, $patchData);

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testUpdateProductFailure(): void
    {
        // Arrange
        $productId = 'PRODUCT-123';
        $patchData = [
            [
                'op' => 'replace',
                'path' => '/description',
                'value' => 'New description',
            ],
        ];

        Http::fake([
            "*/v1/catalogs/products/{$productId}" => Http::response(['message' => 'Update failed'], 400),
        ]);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to update PayPal product '{$productId}': Update failed");

        // Act
        $this->payPalProductService->updateProduct($productId, $patchData);
    }

    public function testDeleteProductThrowsException(): void
    {
        // Arrange
        $productId = 'PRODUCT-123';

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Direct deletion of products is not supported by PayPal Catalog Products API v1. Consider updating product status to 'INACTIVE' if applicable.");

        // Act
        $this->payPalProductService->deleteProduct($productId);
    }
}