<?php

namespace Tests\Unit\Services\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Feature;
use App\Models\MediaProduct;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductReview;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductAdminService;
use App\Services\Product\ProductMediaService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ProductAdminServiceTest extends TestCase
{
    use RefreshDatabase;

    private MockInterface $productMediaServiceMock;
    private MockInterface $productRepositoryMock;
    private ProductAdminService $productAdminService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productMediaServiceMock = Mockery::mock(ProductMediaService::class);
        $this->productRepositoryMock = Mockery::mock(ProductRepository::class);
        $this->productAdminService = new ProductAdminService(
            $this->productMediaServiceMock,
            $this->productRepositoryMock
        );

        // Create a user for testing purposes
        $this->user = User::factory()->create();
        $this->actingAs($this->user); // Authenticate the user
        $this->productAdminService->setUser($this->user); // Set user for service (BaseService Dependency)
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetProductById_ProductExists_ReturnsProduct()
    {
        $product = Product::factory()->create();
        $result = $this->productAdminService->getProductById($product->id);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($product->id, $result->id);
    }

    public function testGetProductById_ProductNotFound_ThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product not found');

        $this->productAdminService->getProductById(999); // Non-existent ID
    }

    public function testInitializeProduct_CreatesProductAndSavesRelations_ReturnsProduct()
    {
        // Mock the saveProductRelations method
        $product = new Product(['active' => false]);

        $result = $this->productAdminService->initializeProduct();

        $this->assertInstanceOf(Product::class, $result);
        $this->assertFalse($result->active);

        $this->assertDatabaseHas('products', ['user_id' => $this->user->id, 'active' => false]);
    }

    public function testSaveProduct_ProductExists_CallsUpdateProduct()
    {
        $product = Product::factory()->create();

        $this->productAdminService = Mockery::mock(ProductAdminService::class, [
            $this->productMediaServiceMock,
            $this->productRepositoryMock
        ])->makePartial()->shouldAllowMockingProtectedMethods();

        $this->productAdminService->shouldReceive('updateProduct')->once()->with($product, [])->andReturn($product);

        $this->productAdminService->saveProduct($product, []);
    }

    public function testSaveProduct_ProductDoesNotExist_CallsCreateProduct()
    {
        $product = new Product();

        $this->productAdminService = Mockery::mock(ProductAdminService::class, [
            $this->productMediaServiceMock,
            $this->productRepositoryMock
        ])->makePartial()->shouldAllowMockingProtectedMethods();

        $this->productAdminService->shouldReceive('createProduct')->once()->with([])->andReturn($product);

        $this->productAdminService->saveProduct($product, []);
    }

    public function testCreateProduct_CreatesProductAndSavesRelations_ReturnsProduct()
    {
        $data = ['title' => 'Test Product', 'name' => 'test-product'];

        $this->productRepositoryMock->shouldReceive('buildCloneEntityStr')
            ->once()
            ->andReturn($data['name']);

        $result = $this->productAdminService->createProduct($data);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($data['name'], $result->name);

        $this->assertDatabaseHas('products', ['user_id' => $this->user->id, 'name' => $data['name']]);
    }

    public function testUpdateProduct_UpdatesProductAndSavesRelations_ReturnsTrue()
    {
        $product = Product::factory()->create();
        $data = ['title' => 'Updated Product'];

        $this->productAdminService = Mockery::mock(ProductAdminService::class, [
            $this->productMediaServiceMock,
            $this->productRepositoryMock
        ])->makePartial()->shouldAllowMockingProtectedMethods();

        $this->productAdminService->shouldReceive('saveProductRelations')->once()->with($product, $data)->andReturn(true);

        $result = $this->productAdminService->updateProduct($product, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'title' => $data['title']]);
    }

    public function testDeleteProduct_DeletesProduct_ReturnsTrue()
    {
        $product = Product::factory()->create();

        $result = $this->productAdminService->deleteProduct($product);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function testSaveProductRelations_SavesRelations_ReturnsTrue()
    {
        $product = Product::factory()->create();
        $feature = Feature::factory()->create();
        $userFollow = User::factory()->create();
        $brand = Brand::factory()->create();
        $color = Color::factory()->create();
        $productCategory = ProductCategory::factory()->create();
        $category = Category::factory()->create();
        $price = Price::factory()->create();
        $review = ['comment' => 'Test Review', 'rating' => 5];

        $data = [
            'features' => [$feature->id],
            'follows' => [$userFollow->id],
            'brands' => [$brand->id],
            'colors' => [$color->id],
            'product_categories' => [$productCategory->id],
            'categories' => [$category->id],
            'prices' => [$price->id],
            'reviews' => [$review],
            'media' => [],
        ];

        $result = $this->productAdminService->saveProductRelations($product, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('feature_product', ['product_id' => $product->id, 'feature_id' => $feature->id]);
        $this->assertDatabaseHas('product_user', ['product_id' => $product->id, 'user_id' => $userFollow->id]);
        $this->assertDatabaseHas('brand_product', ['product_id' => $product->id, 'brand_id' => $brand->id]);
        $this->assertDatabaseHas('color_product', ['product_id' => $product->id, 'color_id' => $color->id]);
        $this->assertDatabaseHas('product_category_product', ['product_id' => $product->id, 'product_category_id' => $productCategory->id]);
        $this->assertDatabaseHas('category_product', ['product_id' => $product->id, 'category_id' => $category->id]);
        $this->assertDatabaseHas('price_product', ['product_id' => $product->id, 'price_id' => $price->id]);
        $this->assertDatabaseHas('product_reviews', ['product_id' => $product->id, 'user_id' => $this->user->id, 'comment' => $review['comment']]);
    }

    public function testBuildImageRequestData_BuildsImageDataArray_ReturnsArray()
    {
        $data = [
            'image_1' => 'image1.jpg',
            'alt_1' => 'Alt Text 1',
            'image_2' => 'image2.jpg',
            'alt_2' => 'Alt Text 2',
            'other_field' => 'value',
        ];

        $expected = [
            [
                'image' => 'image1.jpg',
                'alt' => 'Alt Text 1',
            ],
            [
                'image' => 'image2.jpg',
                'alt' => 'Alt Text 2',
            ],
        ];

        $result = $this->productAdminService->buildImageRequestData($data);

        $this->assertEquals($expected, $result);
    }

    public function testCreateMediaProduct_CreatesMediaProduct_ReturnsTrue()
    {
        $product = Product::factory()->create();
        $data = ['name' => 'Test Media'];

        $this->productAdminService = Mockery::mock(ProductAdminService::class, [
            $this->productMediaServiceMock,
            $this->productRepositoryMock
        ])->makePartial()->shouldAllowMockingProtectedMethods();

        $this->productAdminService->shouldReceive('saveMediaProduct')->once()->with($product, $data)->andReturn(true);

        $result = $this->productAdminService->createMediaProduct($product, $data);

        $this->assertTrue($result);
    }

    public function testSaveMediaProduct_ReturnsTrue()
    {
        $product = Product::factory()->create();
        $data = ['name' => 'Test Media'];

        $result = $this->productAdminService->saveMediaProduct($product, $data);

        $this->assertTrue($result);
    }

    public function testBulkDeleteProducts_DeletesProducts_ReturnsTrue()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $productIds = [$product1->id, $product2->id];

        $result = $this->productAdminService->bulkDeleteProducts($productIds);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('products', ['id' => $product1->id]);
        $this->assertDatabaseMissing('products', ['id' => $product2->id]);
    }

    public function testBulkDeleteProducts_NoProductsFound_ThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No products found for deletion');

        $this->productAdminService->bulkDeleteProducts([999, 1000]); // Non-existent IDs
    }
}