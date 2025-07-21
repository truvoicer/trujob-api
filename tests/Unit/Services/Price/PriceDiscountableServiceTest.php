<?php

namespace Tests\Unit\Services\Price;

use App\Enums\MorphEntity;
use App\Http\Resources\Price\PriceResource;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Price;
use App\Repositories\PriceRepository;
use App\Services\Price\PriceDiscountableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PriceDiscountableServiceTest extends TestCase
{
    use RefreshDatabase;

    private PriceDiscountableService $priceDiscountableService;
    private PriceRepository $priceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->priceRepository = $this->createMock(PriceRepository::class);
        $this->priceDiscountableService = new PriceDiscountableService($this->priceRepository);
    }

    public function testValidateRequestSuccess(): void
    {
        // Arrange
        $price = Price::factory()->create();
        $requestData = ['discountable_id' => $price->id];
        $request = new Request($requestData);
        app()->instance('request', $request);

        // Act
        $result = $this->priceDiscountableService->validateRequest();

        // Assert
        $this->assertTrue($result);
    }

    public function testValidateRequestFails(): void
    {
        // Arrange
        $requestData = ['discountable_id' => 999];
        $request = new Request($requestData);
        app()->instance('request', $request);

        // Act
        try {
            $this->priceDiscountableService->validateRequest();
            $this->fail('Expected ValidationException was not thrown');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('discountable_id', $e->errors());
        }
    }

    public function testAttachDiscountableSuccess(): void
    {
        // Arrange
        $discount = Discount::factory()->create();
        $price = Price::factory()->create();
        $data = ['discountable_id' => $price->id];

        $this->priceRepository->expects($this->once())
            ->method('findById')
            ->with($price->id)
            ->willReturn($price);

        // Act
        $this->priceDiscountableService->attachDiscountable($discount, $data);

        // Assert
        $this->assertDatabaseHas('discountables', [
            'discount_id' => $discount->id,
            'discountable_id' => $price->id,
            'discountable_type' => MorphEntity::CURRENCY
        ]);
    }

    public function testAttachDiscountablePriceNotFound(): void
    {
        // Arrange
        $discount = Discount::factory()->create();
        $data = ['discountable_id' => 999];

        $this->priceRepository->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Price not found');

        // Act
        $this->priceDiscountableService->attachDiscountable($discount, $data);
    }

    public function testDetachDiscountable(): void
    {
        // Arrange
        $discount = Discount::factory()->create();
        $price = Price::factory()->create();
        $data = ['discountable_id' => $price->id];

        $discountable = Discountable::create([
            'discount_id' => $discount->id,
            'discountable_id' => $price->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);

        // Act
        $this->priceDiscountableService->detachDiscountable($discount, $data);

        // Assert
        $this->assertDatabaseMissing('discountables', [
            'discount_id' => $discount->id,
            'discountable_id' => $price->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
    }

    public function testGetDiscountableEntityResourceData(): void
    {
        // Arrange
        $price = Price::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $price->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);

        // Act
        $result = $this->priceDiscountableService->getDiscountableEntityResourceData(new \Illuminate\Http\Resources\Json\JsonResource($discountable));

        // Assert
        $this->assertArrayHasKey('price', $result);
        $this->assertInstanceOf(PriceResource::class, $result['price']);
        $this->assertEquals($price->id, $result['price']->resource->id);
    }

    public function testIsDiscountValidForOrderItemValid(): void
    {
        // Arrange
        $price = Price::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $price->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
        $product = \App\Models\Product::factory()->create();
        $price->products()->attach($product->id);
        $orderItemable = \App\Models\OrderItemable::factory()->create([
            'order_itemable_id' => $product->id,
            'order_itemable_type' => \App\Models\Product::class
        ]);
        $orderItem = OrderItem::factory()->create([
            'order_itemable_id' => $orderItemable->id,
            'order_itemable_type' => \App\Models\OrderItemable::class
        ]);

        // Act
        $result = $this->priceDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsDiscountValidForOrderItemInvalidPriceNotFound(): void
    {
        // Arrange
        $discountable = Discountable::factory()->create([
            'discountable_id' => 999,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
        $orderItem = OrderItem::factory()->create();

        // Act
        $result = $this->priceDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItemInvalidOrderItemableNotFound(): void
    {
        // Arrange
        $price = Price::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $price->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);

        $orderItem = OrderItem::factory()->create([
            'order_itemable_id' => null,
            'order_itemable_type' => null
        ]);

        // Act
        $result = $this->priceDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItemInvalidProductNotFound(): void
    {
        // Arrange
        $price = Price::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $price->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
        $product = \App\Models\Product::factory()->create();
        $orderItemable = \App\Models\OrderItemable::factory()->create([
            'order_itemable_id' => $product->id,
            'order_itemable_type' => \App\Models\Product::class
        ]);
        $orderItem = OrderItem::factory()->create([
            'order_itemable_id' => $orderItemable->id,
            'order_itemable_type' => \App\Models\OrderItemable::class
        ]);

        // Act
        $result = $this->priceDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderValid(): void
    {
        // Arrange
        $price = Price::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $price->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
        $order = Order::factory()->create();

        // Act
        $result = $this->priceDiscountableService->isDiscountValidForOrder($discountable, $order);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsDiscountValidForOrderInvalidPriceNotFound(): void
    {
        // Arrange
        $discountable = Discountable::factory()->create([
            'discountable_id' => 999,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
        $order = Order::factory()->create();

        // Act
        $result = $this->priceDiscountableService->isDiscountValidForOrder($discountable, $order);

        // Assert
        $this->assertFalse($result);
    }
}