<?php

namespace Tests\Unit\Services\Shipping;

use App\Enums\MorphEntity;
use App\Http\Resources\Shipping\ShippingZoneResource;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingZone;
use App\Repositories\ShippingZoneRepository;
use App\Services\Shipping\ShippingZoneDiscountableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ShippingZoneDiscountableServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShippingZoneRepository $shippingZoneRepository;
    private ShippingZoneDiscountableService $shippingZoneDiscountableService;
    private ShippingZone $shippingZone;
    private Discount $discount;


    protected function setUp(): void
    {
        parent::setUp();

        $this->shippingZoneRepository = $this->createMock(ShippingZoneRepository::class);
        $this->shippingZoneDiscountableService = new ShippingZoneDiscountableService($this->shippingZoneRepository);
        $this->shippingZone = ShippingZone::factory()->create();
        $this->discount = Discount::factory()->create();
    }

    public function testValidateRequest_validData_returnsTrue(): void
    {
        $data = ['discountable_id' => $this->shippingZone->id];
        request()->merge($data);

        $this->assertTrue($this->shippingZoneDiscountableService->validateRequest());
    }

    public function testValidateRequest_invalidData_throwsValidationException(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        request()->merge(['discountable_id' => 999]); // Non-existent shipping zone ID

        try {
            $this->shippingZoneDiscountableService->validateRequest();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('discountable_id', $e->errors());
            throw $e;
        }
    }


    public function testAttachDiscountable_validData_attachesDiscountable(): void
    {
        $data = ['discountable_id' => $this->shippingZone->id];

        $this->shippingZoneRepository->expects($this->once())
            ->method('findById')
            ->with($data['discountable_id'])
            ->willReturn($this->shippingZone);

        $this->shippingZoneDiscountableService->attachDiscountable($this->discount, $data);

        $this->assertDatabaseHas('discountables', [
            'discount_id' => $this->discount->id,
            'discountable_id' => $this->shippingZone->id,
            'discountable_type' => $this->shippingZone->getMorphClass(),
        ]);
    }

    public function testAttachDiscountable_invalidShippingZone_throwsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Shipping zone not found');

        $data = ['discountable_id' => 999];

        $this->shippingZoneRepository->expects($this->once())
            ->method('findById')
            ->with($data['discountable_id'])
            ->willReturn(null);

        $this->shippingZoneDiscountableService->attachDiscountable($this->discount, $data);
    }

    public function testDetachDiscountable_validData_detachesDiscountable(): void
    {
        // First, attach a discountable so we can detach it
        $this->shippingZone->discountables()->create([
            'discount_id' => $this->discount->id,
        ]);
        $data = ['discountable_id' => $this->shippingZone->id];

        $this->shippingZoneDiscountableService->detachDiscountable($this->discount, $data);

        $this->assertDatabaseMissing('discountables', [
            'discount_id' => $this->discount->id,
            'discountable_id' => $this->shippingZone->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
    }

    public function testGetDiscountableEntityResourceData_returnsArrayWithShippingZoneResource(): void
    {
        $discountable = new Discountable(['discountable_id' => $this->shippingZone->id, 'discountable_type' => $this->shippingZone->getMorphClass()]);
        $discountable->setRelation('discountable', $this->shippingZone);
        $resource = new \Illuminate\Http\Resources\Json\JsonResource($discountable);

        $result = $this->shippingZoneDiscountableService->getDiscountableEntityResourceData($resource);

        $this->assertArrayHasKey('shipping_zone', $result);
        $this->assertInstanceOf(ShippingZoneResource::class, $result['shipping_zone']);
        $this->assertEquals($this->shippingZone->id, $result['shipping_zone']->resource->id);
    }

    public function testIsDiscountValidForOrderItem_returnsTrue(): void
    {
        $discountable = new Discountable(); // Create a dummy Discountable instance

        $orderItem = new OrderItem(); // Create a dummy OrderItem instance

        $this->assertTrue($this->shippingZoneDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem));
    }

    public function testIsDiscountValidForOrder_returnsTrue(): void
    {
        $discountable = new Discountable(); // Create a dummy Discountable instance
        $order = new Order(); // Create a dummy Order instance

        $this->assertTrue($this->shippingZoneDiscountableService->isDiscountValidForOrder($discountable, $order));
    }
}
