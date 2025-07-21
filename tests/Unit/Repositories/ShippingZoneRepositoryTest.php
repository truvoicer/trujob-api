<?php

namespace Tests\Unit\Repositories;

use App\Models\ShippingZone;
use App\Repositories\ShippingZoneRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingZoneRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ShippingZoneRepository $shippingZoneRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shippingZoneRepository = new ShippingZoneRepository();
    }

    public function testGetModelReturnsShippingZoneModel(): void
    {
        $model = $this->shippingZoneRepository->getModel();

        $this->assertInstanceOf(ShippingZone::class, $model);
    }

    public function testFindByParamsReturnsCollectionOfShippingZones(): void
    {
        // Arrange
        ShippingZone::factory()->count(3)->create();

        // Act
        $shippingZones = $this->shippingZoneRepository->findByParams('name', 'asc');

        // Assert
        $this->assertCount(3, $shippingZones);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $shippingZones);
        $this->assertInstanceOf(ShippingZone::class, $shippingZones->first());
    }

    public function testFindByParamsReturnsLimitedNumberOfShippingZones(): void
    {
        // Arrange
        ShippingZone::factory()->count(5)->create();

        // Act
        $shippingZones = $this->shippingZoneRepository->findByParams('name', 'asc', 2);

        // Assert
        $this->assertCount(2, $shippingZones);
    }


    public function testFindByQueryReturnsCollectionOfShippingZones(): void
    {
        // Arrange
        ShippingZone::factory()->count(2)->create();

        // Act
        $shippingZones = $this->shippingZoneRepository->findByQuery('dummy_query');

        // Assert
        $this->assertCount(2, $shippingZones);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $shippingZones);
        $this->assertInstanceOf(ShippingZone::class, $shippingZones->first());
    }
}
