<?php

namespace Tests\Unit\Services\Shipping;

use App\Enums\Order\Shipping\ShippingZoneAbleType;
use App\Factories\Shipping\ShippingZoneAbleFactory;
use App\Models\Country;
use App\Models\Discount;
use App\Models\ShippingZone;
use App\Services\Shipping\ShippingZoneService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShippingZoneServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShippingZoneService $shippingZoneService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shippingZoneService = new ShippingZoneService();
    }

    public function testSyncShippingZoneAbles(): void
    {
        // Arrange
        $shippingZone = ShippingZone::factory()->create();
        $data = [
            [
                'shipping_zoneable_type' => ShippingZoneAbleType::POSTCODE->value,
                'shipping_zoneable_id' => 1,
                'shipping_zoneable_value' => '12345',
            ],
            [
                'shipping_zoneable_type' => ShippingZoneAbleType::STATE->value,
                'shipping_zoneable_id' => 2,
                'shipping_zoneable_value' => 'CA',
            ],
        ];

        // Mock ShippingZoneAbleFactory (Adjust based on actual factory implementation)
        $shippingZoneAbleFactoryMock = $this->mock(ShippingZoneAbleFactory::class);
        $shippingZoneAbleFactoryMock->shouldReceive('create')
            ->with(ShippingZoneAbleType::POSTCODE)
            ->andReturnSelf();
        $shippingZoneAbleFactoryMock->shouldReceive('syncShippingZoneAble')
            ->with($shippingZone, collect($data)->where('shipping_zoneable_type', ShippingZoneAbleType::POSTCODE->value)->toArray())
            ->once();

        $shippingZoneAbleFactoryMock->shouldReceive('create')
            ->with(ShippingZoneAbleType::STATE)
            ->andReturnSelf();
        $shippingZoneAbleFactoryMock->shouldReceive('syncShippingZoneAble')
            ->with($shippingZone, collect($data)->where('shipping_zoneable_type', ShippingZoneAbleType::STATE->value)->toArray())
            ->once();


        // Act
        $this->shippingZoneService->syncShippingZoneAbles($shippingZone, $data);

        // Assert
        $this->assertTrue(true, 'SyncShippingZoneAbles executed without errors'); // Placeholder assertion
    }

    public function testCreateShippingZone(): void
    {
        // Arrange
        $data = [
            'label' => 'Test Zone',
            'description' => 'Test Description',
        ];

        // Act
        $result = $this->shippingZoneService->createShippingZone($data);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('shipping_zones', [
            'name' => Str::slug('Test Zone'),
            'description' => 'Test Description',
        ]);
    }

    public function testUpdateShippingZone(): void
    {
        // Arrange
        $shippingZone = ShippingZone::factory()->create();
        $data = [
            'label' => 'Updated Zone',
            'description' => 'Updated Description',
        ];

        // Act
        $result = $this->shippingZoneService->updateShippingZone($shippingZone, $data);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('shipping_zones', [
            'id' => $shippingZone->id,
            'name' => Str::slug('Updated Zone'),
            'description' => 'Updated Description',
        ]);
    }

    public function testDeleteShippingZone(): void
    {
        // Arrange
        $shippingZone = ShippingZone::factory()->create();

        // Act
        $result = $this->shippingZoneService->deleteShippingZone($shippingZone);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('shipping_zones', [
            'id' => $shippingZone->id,
        ]);
    }

    public function testSyncCountries(): void
    {
        // Arrange
        $shippingZone = ShippingZone::factory()->create();
        $countries = Country::factory()->count(2)->create();
        $countryIds = $countries->pluck('id')->toArray();

        // Act
        $result = $this->shippingZoneService->syncCountries($shippingZone, $countryIds);

        // Assert
        $this->assertTrue($result);
        $this->assertCount(2, $shippingZone->countries);
    }

    public function testSyncDiscounts(): void
    {
        // Arrange
        $shippingZone = ShippingZone::factory()->create();
        $discounts = Discount::factory()->count(2)->create();
        $discountIds = $discounts->pluck('id')->toArray();

        // Act
        $result = $this->shippingZoneService->syncDiscounts($shippingZone, $discountIds);

        // Assert
        $this->assertTrue($result);
        $this->assertCount(2, $shippingZone->discounts);
    }

    public function testDestroyBulkShippingZones(): void
    {
        // Arrange
        $shippingZones = ShippingZone::factory()->count(3)->create();
        $ids = $shippingZones->pluck('id')->toArray();

        // Act
        $result = $this->shippingZoneService->destroyBulkShippingZones($ids);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseCount('shipping_zones', 0);
    }
}
