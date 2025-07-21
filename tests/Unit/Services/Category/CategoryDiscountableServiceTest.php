<?php

namespace Tests\Unit\Services\Category;

use App\Contracts\Discount\DiscountableInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemable;
use App\Repositories\CategoryRepository;
use App\Services\Category\CategoryDiscountableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Tests\TestCase;

class CategoryDiscountableServiceTest extends TestCase
{
    use RefreshDatabase;

    private CategoryDiscountableService $categoryDiscountableService;
    private CategoryRepository $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = Mockery::mock(CategoryRepository::class);
        $this->categoryDiscountableService = new CategoryDiscountableService($this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testValidateRequestPassesValidation(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $requestData = ['discountable_id' => $category->id];
        request()->merge($requestData);

        // Act
        $result = $this->categoryDiscountableService->validateRequest();

        // Assert
        $this->assertTrue($result);
    }

    public function testValidateRequestFailsValidation(): void
    {
        // Arrange
        $requestData = ['discountable_id' => 999]; // Non-existent category ID
        request()->merge($requestData);

        // Assert
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        // Act
        $this->categoryDiscountableService->validateRequest();
    }


    public function testAttachDiscountable(): void
    {
        // Arrange
        $discount = Discount::factory()->create();
        $category = Category::factory()->create();
        $data = ['discountable_id' => $category->id];

        $this->categoryRepository->shouldReceive('findById')
            ->with($category->id)
            ->once()
            ->andReturn($category);

        // Mock the Category model's discountables() method to return a mock relation
        $relationMock = Mockery::mock();
        $relationMock->shouldReceive('create')
            ->with(['discount_id' => $discount->id])
            ->once()
            ->andReturn(new Discountable()); // You can return a mock Discountable here if needed

        $categoryMock = Mockery::mock(Category::class);
        $categoryMock->shouldReceive('discountables')->andReturn($relationMock);

        $this->categoryRepository->shouldReceive('findById')
            ->with($data['discountable_id'])
            ->andReturn($categoryMock);

        // Act
        $this->categoryDiscountableService->attachDiscountable($discount, $data);

        // Assert
        $this->assertTrue(true); // Assert that no exception was thrown
    }

    public function testAttachDiscountableThrowsExceptionWhenCategoryNotFound(): void
    {
        // Arrange
        $discount = Discount::factory()->create();
        $data = ['discountable_id' => 999]; // Non-existent category ID

        $this->categoryRepository->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category not found');

        // Act
        $this->categoryDiscountableService->attachDiscountable($discount, $data);
    }

    public function testDetachDiscountable(): void
    {
        // Arrange
        $discount = Discount::factory()->create();
        $category = Category::factory()->create();
        $discountable = Discountable::factory()->create([
            'discount_id' => $discount->id,
            'discountable_id' => $category->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);

        $data = ['discountable_id' => $category->id];

        // Act
        $this->categoryDiscountableService->detachDiscountable($discount, $data);

        // Assert
        $this->assertDatabaseMissing('discountables', [
            'discount_id' => $discount->id,
            'discountable_id' => $category->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
    }

    public function testGetDiscountableEntityResourceData(): void
    {
        // Arrange
        $discountable = Discountable::factory()->create();
        $discountable->discountable()->associate(Category::factory()->create());

        $resource = new JsonResource($discountable);

        // Act
        $result = $this->categoryDiscountableService->getDiscountableEntityResourceData($resource);

        // Assert
        $this->assertArrayHasKey('category', $result);
        $this->assertInstanceOf(CategoryResource::class, $result['category']);
    }

    public function testIsDiscountValidForOrderItemReturnsFalseWhenCategoryNotFound(): void
    {
        // Arrange
        $discountable = Discountable::factory()->create(['discountable_id' => 999]);
        $orderItem = OrderItem::factory()->create();

        // Act
        $result = $this->categoryDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItemReturnsFalseWhenOrderItemableNotFound(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $discountable = Discountable::factory()->create(['discountable_id' => $category->id]);
        $orderItem = OrderItem::factory()->create(['order_itemable_id' => null, 'order_itemable_type' => null]);

        // Act
        $result = $this->categoryDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItemReturnsFalseWhenCategoryNotAssociatedWithOrderItemable(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $discountable = Discountable::factory()->create(['discountable_id' => $category->id]);
        $orderItemable = OrderItemable::factory()->create();
        $orderItem = OrderItem::factory()->create(['order_itemable_id' => $orderItemable->id, 'order_itemable_type' => 'App\Models\OrderItemable']);

        // Act
        $result = $this->categoryDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItemReturnsTrue(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $discountable = Discountable::factory()->create(['discountable_id' => $category->id]);
        $orderItemable = OrderItemable::factory()->create();
        $orderItem = OrderItem::factory()->create(['order_itemable_id' => $orderItemable->id, 'order_itemable_type' => 'App\Models\OrderItemable']);
        $orderItemable->categories()->attach($category);

        // Act
        $result = $this->categoryDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsDiscountValidForOrderReturnsFalseWhenCategoryNotFound(): void
    {
        // Arrange
        $discountable = Discountable::factory()->create(['discountable_id' => 999]);
        $order = Order::factory()->create();

        // Act
        $result = $this->categoryDiscountableService->isDiscountValidForOrder($discountable, $order);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderReturnsTrue(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $discountable = Discountable::factory()->create(['discountable_id' => $category->id]);
        $order = Order::factory()->create();

        // Act
        $result = $this->categoryDiscountableService->isDiscountValidForOrder($discountable, $order);

        // Assert
        $this->assertTrue($result);
    }
}
