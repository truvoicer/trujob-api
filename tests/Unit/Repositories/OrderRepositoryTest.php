<?php

namespace Tests\Unit\Repositories;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = new OrderRepository();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->orderRepository);
    }

    /** @test */
    public function it_can_get_the_model()
    {
        $model = $this->orderRepository->getModel();

        $this->assertInstanceOf(Order::class, $model);
    }

    /** @test */
    public function it_can_find_by_params()
    {
        // Arrange
        Order::factory()->count(3)->create();
        $sort = 'id';
        $order = 'asc';
        $count = 2;

        // Act
        $orders = $this->orderRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $orders);
        $this->assertEquals(1, $orders[0]->id);
        $this->assertEquals(2, $orders[1]->id);
    }

    /** @test */
    public function it_can_find_by_query()
    {
        // Arrange
        Order::factory()->count(5)->create();

        // Act
        $orders = $this->orderRepository->findByQuery('some_query');

        // Assert
        $this->assertCount(5, $orders);
    }
}
