<?php

namespace Tests\Unit\Services\Region;

use App\Enums\MorphEntity;
use App\Http\Resources\Region\RegionResource;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Region;
use App\Models\UserSetting;
use App\Repositories\RegionRepository;
use App\Services\Region\RegionDiscountableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegionDiscountableServiceTest extends TestCase
{
    use RefreshDatabase;

    private RegionDiscountableService $regionDiscountableService;
    private RegionRepository $regionRepository;
    private Region $region;
    private Discount $discount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->regionRepository = $this->createMock(RegionRepository::class);
        $this->regionDiscountableService = new RegionDiscountableService($this->regionRepository);
        $this->region = Region::factory()->create();
        $this->discount = Discount::factory()->create();
    }

    public function testValidateRequestPassesWithValidDiscountableId(): void
    {
        $data = ['discountable_id' => $this->region->id];
        request()->merge($data);

        $this->assertTrue($this->regionDiscountableService->validateRequest());
    }

    public function testValidateRequestFailsWithInvalidDiscountableId(): void
    {
        $data = ['discountable_id' => 999];
        request()->merge($data);

        $validator = Validator::make(request()->all(), ['discountable_id' => 'exists:regions,id']);

        $this->assertTrue($validator->fails());
    }

    public function testAttachDiscountable(): void
    {
        $data = ['discountable_id' => $this->region->id];

        $this->regionRepository->expects($this->once())
            ->method('findById')
            ->with($this->region->id)
            ->willReturn($this->region);

        $this->regionDiscountableService->attachDiscountable($this->discount, $data);

        $this->assertDatabaseHas('discountables', [
            'discount_id' => $this->discount->id,
            'discountable_id' => $this->region->id,
            'discountable_type' => MorphEntity::REGION->value,
        ]);
    }

    public function testAttachDiscountableThrowsExceptionIfRegionNotFound(): void
    {
        $data = ['discountable_id' => 999];

        $this->regionRepository->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Region not found');

        $this->regionDiscountableService->attachDiscountable($this->discount, $data);
    }

    public function testDetachDiscountable(): void
    {
        $discountable = Discountable::factory()->create([
            'discount_id' => $this->discount->id,
            'discountable_id' => $this->region->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);

        $data = ['discountable_id' => $this->region->id];

        $this->regionDiscountableService->detachDiscountable($this->discount, $data);

        $this->assertDatabaseMissing('discountables', [
            'id' => $discountable->id,
        ]);
    }

    public function testGetDiscountableEntityResourceData(): void
    {
        $discountable = Discountable::factory()->create([
            'discountable_id' => $this->region->id,
            'discountable_type' => MorphEntity::REGION,
        ]);

        $resource = new \Illuminate\Http\Resources\Json\JsonResource($discountable);

        $data = $this->regionDiscountableService->getDiscountableEntityResourceData($resource);

        $this->assertArrayHasKey('region', $data);
        $this->assertInstanceOf(RegionResource::class, $data['region']);
        $this->assertEquals($this->region->id, $data['region']->resource->id);
    }

    public function testIsDiscountValidForOrderItemReturnsFalseWhenRegionNotFound(): void
    {
        $discountable = Discountable::factory()->make(['discountable_id' => 999]);
        $orderItem = OrderItem::factory()->make();

        $result = $this->regionDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItemReturnsFalseWhenOrderItemableNotFound(): void
    {
        $discountable = Discountable::factory()->make(['discountable_id' => $this->region->id]);
        $orderItem = OrderItem::factory()->make(['order_itemable_id' => null, 'order_itemable_type' => null]);

        $result = $this->regionDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItemReturnsFalseWhenUserSettingDoesntExists(): void
    {
        $discountable = Discountable::factory()->make(['discountable_id' => $this->region->id]);
        $orderItem = OrderItem::factory()->create();
        $user = $orderItem->order->user;

        Sanctum::actingAs($user, ['*']);

        $result = $this->regionDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItemReturnsTrueWhenUserSettingExists(): void
    {
        $discountable = Discountable::factory()->make(['discountable_id' => $this->region->id]);
        $orderItem = OrderItem::factory()->create();
        $user = $orderItem->order->user;
        Sanctum::actingAs($user, ['*']);

        $userSetting = UserSetting::factory()->create(['user_id' => $user->id]);
        $userSetting->region()->attach($this->region->id);

        $result = $this->regionDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        $this->assertTrue($result);
    }

    public function testIsDiscountValidForOrderReturnsFalseWhenRegionNotFound(): void
    {
        $discountable = Discountable::factory()->make(['discountable_id' => 999]);
        $order = Order::factory()->make();

        $result = $this->regionDiscountableService->isDiscountValidForOrder($discountable, $order);

        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderReturnsTrue(): void
    {
        $discountable = Discountable::factory()->make(['discountable_id' => $this->region->id]);
        $order = Order::factory()->make();

        $result = $this->regionDiscountableService->isDiscountValidForOrder($discountable, $order);

        $this->assertTrue($result);
    }
}
