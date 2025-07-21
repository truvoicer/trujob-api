<?php

namespace Tests\Unit\Services\Locale;

use App\Contracts\Discount\DiscountableInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Currency\CurrencyResource;
use App\Models\Currency;
use App\Models\Discount;
use App\Models\Discountable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\UserSetting;
use App\Repositories\CurrencyRepository;
use App\Services\Locale\CurrencyDiscountableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CurrencyDiscountableServiceTest extends TestCase
{
    use RefreshDatabase;

    private MockInterface|CurrencyRepository $currencyRepositoryMock;
    private CurrencyDiscountableService $currencyDiscountableService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyRepositoryMock = Mockery::mock(CurrencyRepository::class);
        $this->currencyDiscountableService = new CurrencyDiscountableService(
            $this->currencyRepositoryMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testValidateRequest_validData_returnsTrue(): void
    {
        $currency = Currency::factory()->create();

        $requestData = ['discountable_id' => $currency->id];
        app()->instance('request', new Request($requestData));

        $result = $this->currencyDiscountableService->validateRequest();

        $this->assertTrue($result);
    }

    public function testValidateRequest_invalidData_validationFails(): void
    {
        $requestData = ['discountable_id' => 999]; // Non-existent currency ID
        app()->instance('request', new Request($requestData));

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        try {
            $this->currencyDiscountableService->validateRequest();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('discountable_id', $e->errors());
            throw $e;
        }
    }

    public function testAttachDiscountable_currencyFound_createsDiscountable(): void
    {
        $discount = Discount::factory()->create();
        $currency = Currency::factory()->create();
        $data = ['discountable_id' => $currency->id];

        $this->currencyRepositoryMock
            ->shouldReceive('findById')
            ->with($currency->id)
            ->once()
            ->andReturn($currency);

        $this->currencyDiscountableService->attachDiscountable($discount, $data);

        $this->assertDatabaseHas('discountables', [
            'discount_id' => $discount->id,
            'discountable_id' => $currency->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
    }

    public function testAttachDiscountable_currencyNotFound_throwsException(): void
    {
        $discount = Discount::factory()->create();
        $data = ['discountable_id' => 123];

        $this->currencyRepositoryMock
            ->shouldReceive('findById')
            ->with(123)
            ->once()
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Currency not found');

        $this->currencyDiscountableService->attachDiscountable($discount, $data);
    }

    public function testDetachDiscountable_discountableExists_deletesDiscountable(): void
    {
        $discount = Discount::factory()->create();
        $currency = Currency::factory()->create();
        $data = ['discountable_id' => $currency->id];

        $discountable = Discountable::create([
            'discount_id' => $discount->id,
            'discountable_id' => $currency->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);

        $this->currencyDiscountableService->detachDiscountable($discount, $data);

        $this->assertDatabaseMissing('discountables', [
            'id' => $discountable->id,
        ]);
    }

    public function testGetDiscountableEntityResourceData_returnsCurrencyResource(): void
    {
        $currency = Currency::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $currency->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);

        $mockResource = Mockery::mock(JsonResource::class);
        $mockResource->discountable = $discountable;

        $result = $this->currencyDiscountableService->getDiscountableEntityResourceData($mockResource);

        $this->assertArrayHasKey('currency', $result);
        $this->assertInstanceOf(CurrencyResource::class, $result['currency']);
        $this->assertEquals($currency->id, $result['currency']->resource->id);
    }

    public function testIsDiscountValidForOrderItem_currencyNotFound_returnsFalse(): void
    {
        $discountable = Discountable::factory()->create([
            'discountable_id' => 999, // Non-existent currency
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
        $orderItem = OrderItem::factory()->create();

        $result = $this->currencyDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItem_orderItemableNotFound_returnsFalse(): void
    {
        $currency = Currency::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $currency->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
        $orderItem = OrderItem::factory()->create(['order_itemable_id' => null, 'order_itemable_type' => null]);

        $result = $this->currencyDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItem_userSettingNotFound_returnsFalse(): void
    {
        $currency = Currency::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $currency->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);

        $orderItem = OrderItem::factory()->create();
        $orderItem->orderItemable()->associate(Currency::factory()->create());
        $orderItem->save();

        $user = User::factory()->create();
        $this->actingAs($user);

        $result = $this->currencyDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrderItem_userSettingFound_returnsTrue(): void
    {
        $currency = Currency::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $currency->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);

        $orderItem = OrderItem::factory()->create();
        $orderItem->orderItemable()->associate(Currency::factory()->create());
        $orderItem->save();

        $user = User::factory()->create();
        $userSetting = UserSetting::factory()->create(['user_id' => $user->id]);
        $userSetting->currency()->associate($currency);
        $userSetting->save();
        $this->actingAs($user);

        $result = $this->currencyDiscountableService->isDiscountValidForOrderItem($discountable, $orderItem);

        $this->assertTrue($result);
    }

    public function testIsDiscountValidForOrder_currencyNotFound_returnsFalse(): void
    {
        $discountable = Discountable::factory()->create([
            'discountable_id' => 999, // Non-existent currency
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
        $order = Order::factory()->create();

        $result = $this->currencyDiscountableService->isDiscountValidForOrder($discountable, $order);

        $this->assertFalse($result);
    }

    public function testIsDiscountValidForOrder_currencyFound_returnsTrue(): void
    {
        $currency = Currency::factory()->create();
        $discountable = Discountable::factory()->create([
            'discountable_id' => $currency->id,
            'discountable_type' => MorphEntity::CURRENCY,
        ]);
        $order = Order::factory()->create();

        $result = $this->currencyDiscountableService->isDiscountValidForOrder($discountable, $order);

        $this->assertTrue($result);
    }
}
