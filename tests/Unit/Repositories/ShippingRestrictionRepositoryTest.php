<?php

namespace Tests\Unit\Repositories;

use App\Models\ShippingRestriction;
use App\Repositories\ShippingRestrictionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingRestrictionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ShippingRestrictionRepository $shippingRestrictionRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shippingRestrictionRepository = new ShippingRestrictionRepository();
    }

    public function testGetModelReturnsShippingRestrictionModel(): void
    {
        $model = $this->shippingRestrictionRepository->getModel();

        $this->assertInstanceOf(ShippingRestriction::class, $model);
    }

    public function testFindByParamsReturnsCollectionOfShippingRestrictions(): void
    {
        // Arrange
        ShippingRestriction::factory()->count(3)->create();

        // Act
        $result = $this->shippingRestrictionRepository->findByParams('id', 'asc');

        // Assert
        $this->assertCount(3, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        foreach ($result as $item) {
            $this->assertInstanceOf(ShippingRestriction::class, $item);
        }
    }

    public function testFindByQueryParamsReturnsCollectionOfShippingRestrictions(): void
    {
        // Arrange
        ShippingRestriction::factory()->count(2)->create();

        // Act
        $result = $this->shippingRestrictionRepository->findByQuery('some_query'); // The query doesn't affect the result, it calls findAll()

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        foreach ($result as $item) {
            $this->assertInstanceOf(ShippingRestriction::class, $item);
        }
    }
}