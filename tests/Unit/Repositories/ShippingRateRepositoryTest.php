<?php

namespace Tests\Unit\Repositories;

use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use App\Models\User;
use App\Repositories\ShippingRateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingRateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ShippingRateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ShippingRateRepository();
    }

    public function testGetModelReturnsInstanceOfShippingRate()
    {
        $model = $this->repository->getModel();
        $this->assertInstanceOf(ShippingRate::class, $model);
    }

    public function testFindByParamsReturnsCollectionOfShippingRates()
    {

        $shippingMethod = ShippingMethod::factory()->create();
        $shippingZone = ShippingZone::factory()->create();
        ShippingRate::factory()->count(3)->create([
            'shipping_method_id' => $shippingMethod->id,
            'shipping_zone_id' => $shippingZone->id,
        ]);

        $result = $this->repository->findByParams('id', 'asc');

        $this->assertCount(3, $result);
    }

    public function testFindByParamsWithCountReturnsCorrectNumberOfShippingRates()
    {
        $shippingMethod = ShippingMethod::factory()->create();
        $shippingZone = ShippingZone::factory()->create();
        ShippingRate::factory()->count(5)->create([
            'shipping_method_id' => $shippingMethod->id,
            'shipping_zone_id' => $shippingZone->id,
        ]);

        $result = $this->repository->findByParams('id', 'asc', 2);

        $this->assertCount(2, $result);
    }


    public function testFindByQueryParamsReturnsAllShippingRates()
    {
        $shippingMethod = ShippingMethod::factory()->create();
        $shippingZone = ShippingZone::factory()->create();
        ShippingRate::factory()->count(2)->create([
            'shipping_method_id' => $shippingMethod->id,
            'shipping_zone_id' => $shippingZone->id,
        ]);
        $query = ShippingRate::query();

        $result = $this->repository->findByQuery($query);

        $this->assertCount(2, $result);
    }

}
