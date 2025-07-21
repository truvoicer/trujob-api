<?php

namespace Tests\Unit\Repositories;

use App\Models\OrderItem;
use App\Repositories\OrderItemRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var OrderItemRepository
     */
    private $orderItemRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderItemRepository = new OrderItemRepository();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->orderItemRepository);
    }

    
    public function it_can_get_the_model(): void
    {
        $model = $this->orderItemRepository->getModel();

        $this->assertInstanceOf(OrderItem::class, $model);
    }

    
    public function it_can_find_by_params(): void
    {
        // Arrange
        OrderItem::factory()->count(3)->create();
        $sort = 'id';
        $order = 'asc';

        // Act
        $result = $this->orderItemRepository->findByParams($sort, $order);

        // Assert
        $this->assertCount(3, $result);
        $this->assertInstanceOf(OrderItem::class, $result->first());
        $this->assertEquals(1, $result->first()->id);
    }

    
    public function it_can_find_by_query(): void
    {
        // Arrange
        OrderItem::factory()->count(2)->create();

        // Act
        $result = $this->orderItemRepository->findByQuery([]);

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(OrderItem::class, $result->first());
    }
}
