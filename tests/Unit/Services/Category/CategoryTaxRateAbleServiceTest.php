<?php

namespace Tests\Unit\Services\Category;

use App\Contracts\Tax\TaxRateAbleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TaxRate;
use App\Models\TaxRateAble;
use App\Repositories\CategoryRepository;
use App\Services\Category\CategoryTaxRateAbleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Mockery;

class CategoryTaxRateAbleServiceTest extends TestCase
{
    use RefreshDatabase;

    private CategoryRepository $categoryRepository;
    private CategoryTaxRateAbleService $categoryTaxRateAbleService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = Mockery::mock(CategoryRepository::class);
        $this->categoryTaxRateAbleService = new CategoryTaxRateAbleService($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testValidateRequest_ValidData_ReturnsTrue(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $requestData = ['tax_rateable_id' => $category->id];
        $request = new Request($requestData);
        app()->instance('request', $request);

        // Act
        $result = $this->categoryTaxRateAbleService->validateRequest();

        // Assert
        $this->assertTrue($result);
    }

     public function testValidateRequest_InvalidData_ValidationFails(): void
    {
        // Arrange
        $requestData = ['tax_rateable_id' => 999]; // Non-existent category ID
        $request = new Request($requestData);
        app()->instance('request', $request);

        // Assert
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        // Act
        $this->categoryTaxRateAbleService->validateRequest();
    }

    public function testAttachTaxRateAble_ValidData_CreatesTaxRateAble(): void
    {
        // Arrange
        $taxRate = TaxRate::factory()->create();
        $category = Category::factory()->create();
        $data = ['tax_rateable_id' => $category->id];

        $this->categoryRepository->shouldReceive('findById')
            ->with($category->id)
            ->andReturn($category);

        // Act
        $this->categoryTaxRateAbleService->attachTaxRateAble($taxRate, $data);

        // Assert
        $this->assertDatabaseHas('tax_rate_ables', [
            'tax_rate_id' => $taxRate->id,
            'tax_rateable_id' => $category->id,
            'tax_rateable_type' => MorphEntity::CATEGORY,
        ]);
    }

    public function testAttachTaxRateAble_CategoryNotFound_ThrowsException(): void
    {
        // Arrange
        $taxRate = TaxRate::factory()->create();
        $data = ['tax_rateable_id' => 999];

        $this->categoryRepository->shouldReceive('findById')
            ->with(999)
            ->andReturn(null);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category not found');

        // Act
        $this->categoryTaxRateAbleService->attachTaxRateAble($taxRate, $data);
    }

    public function testDetachTaxRateAble_ValidData_DeletesTaxRateAble(): void
    {
        // Arrange
        $taxRate = TaxRate::factory()->create();
        $category = Category::factory()->create();
        $data = ['tax_rateable_id' => $category->id];
        $taxRateAble = TaxRateAble::factory()->create([
            'tax_rate_id' => $taxRate->id,
            'tax_rateable_id' => $category->id,
            'tax_rateable_type' => MorphEntity::CATEGORY,
        ]);

        // Act
        $this->categoryTaxRateAbleService->detachTaxRateAble($taxRate, $data);

        // Assert
        $this->assertDatabaseMissing('tax_rate_ables', [
            'id' => $taxRateAble->id,
        ]);
    }

    public function testGetTaxRateableEntityResourceData_ValidData_ReturnsArrayWithCategoryResource(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $taxRateAble = TaxRateAble::factory()->make([
            'tax_rateable_id' => $category->id,
            'tax_rateable_type' => MorphEntity::CATEGORY,
        ]);
        $taxRateAble->tax_rateable = $category; // Simulate relation

        $resource = new JsonResource($taxRateAble);

        // Act
        $result = $this->categoryTaxRateAbleService->getTaxRateableEntityResourceData($resource);

        // Assert
        $this->assertArrayHasKey('category', $result);
        $this->assertInstanceOf(CategoryResource::class, $result['category']);
        $this->assertEquals($category->id, $result['category']->resource->id);
    }

    public function testIsTaxRateValidForOrderItem_ValidData_ReturnsTrue(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $taxRateAble = TaxRateAble::factory()->create([
            'tax_rateable_id' => $category->id,
            'tax_rateable_type' => MorphEntity::CATEGORY,
        ]);
        $orderItemable = Category::factory()->create();
        $orderItem = OrderItem::factory()->create(['order_itemable_id' => $orderItemable->id, 'order_itemable_type' => MorphEntity::CATEGORY]);
        $orderItemable->categories()->attach($orderItemable->id);


        // Act
        $result = $this->categoryTaxRateAbleService->isTaxRateValidForOrderItem($taxRateAble, $orderItem);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsTaxRateValidForOrderItem_CategoryNotFound_ReturnsFalse(): void
    {
        // Arrange
        $taxRateAble = TaxRateAble::factory()->create([
            'tax_rateable_id' => 999,
            'tax_rateable_type' => MorphEntity::CATEGORY,
        ]);
        $orderItem = OrderItem::factory()->create();

        // Act
        $result = $this->categoryTaxRateAbleService->isTaxRateValidForOrderItem($taxRateAble, $orderItem);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsTaxRateValidForOrderItem_OrderItemableNotFound_ReturnsFalse(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $taxRateAble = TaxRateAble::factory()->create([
            'tax_rateable_id' => $category->id,
            'tax_rateable_type' => MorphEntity::CATEGORY,
        ]);
        $orderItem = OrderItem::factory()->create(['order_itemable_id' => 1, 'order_itemable_type' => MorphEntity::CATEGORY]);

        // Act
        $result = $this->categoryTaxRateAbleService->isTaxRateValidForOrderItem($taxRateAble, $orderItem);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsTaxRateValidForOrderItem_CategoryNotAssociatedWithOrderItemable_ReturnsFalse(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $taxRateAble = TaxRateAble::factory()->create([
            'tax_rateable_id' => $category->id,
            'tax_rateable_type' => MorphEntity::CATEGORY,
        ]);

        $otherCategory = Category::factory()->create();

        $orderItem = OrderItem::factory()->create(['order_itemable_id' => $otherCategory->id, 'order_itemable_type' => MorphEntity::CATEGORY]);
        $otherCategory->categories()->attach($otherCategory->id);

        // Act
        $result = $this->categoryTaxRateAbleService->isTaxRateValidForOrderItem($taxRateAble, $orderItem);

        // Assert
        $this->assertFalse($result);
    }



    public function testIsTaxRateValidForOrder_ValidData_ReturnsTrue(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $taxRateAble = TaxRateAble::factory()->create([
            'tax_rateable_id' => $category->id,
            'tax_rateable_type' => MorphEntity::CATEGORY,
        ]);
        $order = Order::factory()->create();

        // Act
        $result = $this->categoryTaxRateAbleService->isTaxRateValidForOrder($taxRateAble, $order);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsTaxRateValidForOrder_CategoryNotFound_ReturnsFalse(): void
    {
        // Arrange
        $taxRateAble = TaxRateAble::factory()->create([
            'tax_rateable_id' => 999,
            'tax_rateable_type' => MorphEntity::CATEGORY,
        ]);
        $order = Order::factory()->create();

        // Act
        $result = $this->categoryTaxRateAbleService->isTaxRateValidForOrder($taxRateAble, $order);

        // Assert
        $this->assertFalse($result);
    }
}