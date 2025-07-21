<?php

namespace Tests\Unit\Services\Shipping;

use App\Enums\MorphEntity;
use App\Http\Resources\Shipping\ShippingMethodResource;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use App\Repositories\ShippingMethodRepository;
use App\Services\Shipping\ShippingMethodDiscountableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ShippingMethodDiscountableServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShippingMethodDiscountableService $shippingMethodDiscountableService;
    private ShippingMethodRepository $shippingMethodRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shippingMethodRepository = $this->createMock(ShippingMethodRepository::class);
        $this->shippingMethodDiscountableService = new ShippingMethodDiscountableService(
            $this->shippingMethodRepository
        );
    }

    public function testValidateRequestSuccess(): void
    {
        $shippingMethod = ShippingMethod::factory()->create();
        $requestData = ['discountable_id' => $shippingMethod->id];
        $request = new Request($requestData);
        \Illuminate\Support\Facades\App::instance('request', $request);

        $this->assertTrue($this->shippingMethodDiscountableService->validateRequest());
    }

    public function testValidateRequestFails(): void
    {
        $requestData = ['discountable_id' => 999]; // Non-existent ID
        $request = new Request($requestData);
        \Illuminate\Support\Facades\App::instance('request', $request);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->shippingMethodDiscountableService->validateRequest();
    }


    public function testAttachDiscountableSuccess(): void
    {
        $discount = Discount::factory()->create();
        $shippingMethod = ShippingMethod::factory()->create();
        $data = ['discountable_id' => $shippingMethod->id];

        $this->shippingMethodRepository->expects($this->once())
            ->method('findById')
            ->with($shippingMethod->id)
            ->willReturn($shippingMethod);

        $this->shippingMethodDiscountableService->attachDiscountable($discount, $data);

        $this->assertDatabaseHas('discountables', [
            'discount_id' => $discount->id,
            'discountable_id' => $shippingMethod->id,
            'discountable_type' => MorphEntity::SHIPPING_METHOD->value,
        ]);
    }

    public function testAttachDiscountableThrowsExceptionWhenShippingMethodNotFound(): void
    {
        $discount = Discount::factory()->create();
        $data = ['discountable_id' => 123];

        $this->shippingMethodRepository->expects($this->once())
            ->method('findById')
            ->with(123)
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Shipping zone not found');

        $this->shippingMethodDiscountableService->attachDiscountable($discount, $data);
    }

    public function testDetachDiscountable(): void
    {
        $discount = Discount::factory()->create();
        $shippingMethod = ShippingMethod::factory()->create();
        $data = ['discountable_id' => $shippingMethod->id];

        // Create a Discountable record to be detached
        $discountable = new Discountable([
            'discount_id' => $discount->id,
            'discountable_id' => $shippingMethod->id,
            'discountable_type' => MorphEntity::CURRENCY->value,
        ]);
        $shippingMethod->discountables()->save($discountable);

        $this->shippingMethodDiscountableService->detachDiscountable($discount, $data);

        $this->assertDatabaseMissing('discountables', [
            'discount_id' => $discount->id,
            'discountable_id' => $shippingMethod->id,
            'discountable_type' => MorphEntity::CURRENCY->value,
        ]);
    }

    public function testGetDiscountableEntityResourceData(): void
    {
        $shippingMethod = ShippingMethod::factory()->create();
        $discountable = new Discountable([
            'discountable_id' => $shippingMethod->id,
            'discountable_type' => MorphEntity::SHIPPING_METHOD->value,
        ]);

        $resource = new JsonResource($discountable);

        $result = $this->shippingMethodDiscountableService->getDiscountableEntityResourceData($resource);

        $this->assertArrayHasKey('shipping_method', $result);
        $this->assertInstanceOf(ShippingMethodResource::class, $result['shipping_method']);
        $this->assertEquals($shippingMethod->id, $result['shipping_method']->resource->id);
    }

    public function testIsDiscountValidForOrderItem(): void
    {
        $discountable = Discountable::factory()->create();
        $orderItem = OrderItem::factory()->create();

        $this->assertTrue($this->shippingMethodDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem)); // Placeholder, always true
    }

    public function testIsDiscountValidForOrderValid(): void
    {
        $shippingMethod = ShippingMethod::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $shippingMethod->id,
            'discountable_type' => MorphEntity::SHIPPING_METHOD->value,
        ]);
        $order = Order::factory()->create();

        $this->shippingMethodRepository->expects($this->once())
            ->method('findById')
            ->with($discountable->discountable_id)
            ->willReturn($shippingMethod);

        $this->assertTrue($this->shippingMethodDiscountableService->isDiscountValidForOrder($discountable, $order));
    }

    public function testIsDiscountValidForOrderInvalidShippingMethodNotFound(): void
    {
        $discountable = Discountable::factory()->create([
            'discountable_id' => 999,
            'discountable_type' => MorphEntity::SHIPPING_METHOD->value,
        ]);
        $order = Order::factory()->create();

        $this->shippingMethodRepository->expects($this->once())
            ->method('findById')
            ->with($discountable->discountable_id)
            ->willReturn(null);

        $this->assertFalse($this->shippingMethodDiscountableService->isDiscountValidForOrder($discountable, $order));
    }
}
