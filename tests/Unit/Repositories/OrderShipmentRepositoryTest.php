<?php

namespace Tests\Unit\Repositories;

use App\Models\OrderShipment;
use App\Repositories\OrderShipmentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShipmentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected OrderShipmentRepository $orderShipmentRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderShipmentRepository = new OrderShipmentRepository();
    }

    
    public function test_it_can_get_the_model()
    {
        $model = $this->orderShipmentRepository->getModel();
        $this->assertInstanceOf(OrderShipment::class, $model);
    }

    
    public function test_it_can_find_by_params()
    {
        // Arrange
        OrderShipment::factory()->count(3)->create();
        $sort = 'id';
        $order = 'asc';
        $count = 2;

        // Act
        $result = $this->orderShipmentRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $result);
        $this->assertEquals(OrderShipment::orderBy($sort, $order)->limit($count)->get()->toArray(), $result->toArray());
    }

    
    public function test_it_can_find_by_query()
    {
        // Arrange
        OrderShipment::factory()->count(3)->create();

        // Act
        $result = $this->orderShipmentRepository->findByQuery([]);

        // Assert
        $this->assertCount(3, $result);
        $this->assertEquals(OrderShipment::all()->toArray(), $result->toArray());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->orderShipmentRepository);
    }
}
