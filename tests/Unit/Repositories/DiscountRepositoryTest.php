<?php

namespace Tests\Unit\Repositories;

use App\Models\Discount;
use App\Repositories\DiscountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private DiscountRepository $discountRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discountRepository = new DiscountRepository();
    }

    public function testGetModel(): void
    {
        $model = $this->discountRepository->getModel();

        $this->assertInstanceOf(Discount::class, $model);
    }

    public function testFindByParams(): void
    {
        // Arrange
        Discount::factory()->count(3)->create();
        $sort = 'id';
        $order = 'asc';
        $count = 2;

        // Act
        $result = $this->discountRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertInstanceOf(Discount::class, $result->first());

        $ids = $result->pluck('id')->toArray();
        $this->assertEquals([1,2], $ids);
    }

    public function testFindByQueryParams(): void
    {
        // Arrange
        Discount::factory()->count(2)->create();

        // Act
        $result = $this->discountRepository->findByQuery([]);

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertInstanceOf(Discount::class, $result->first());
    }
}
