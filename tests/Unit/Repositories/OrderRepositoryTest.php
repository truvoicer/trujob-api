<?php

namespace Tests\Unit\Repositories;

use App\Models\Currency;
use App\Models\Order;
use App\Models\User;
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


    public function test_it_can_get_the_model()
    {
        $model = $this->orderRepository->getModel();

        $this->assertInstanceOf(Order::class, $model);
    }


    public function test_it_can_find_by_params()
    {
        // Arrange
        $user = User::factory()->create();
        $currency = Currency::factory()->create();
        Order::factory()->count(3)->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);
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


    public function test_it_can_find_by_query()
    {
        // Arrange
        $user = User::factory()->create();
        $currency = Currency::factory()->create();
        Order::factory()->count(5)->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);

        // Act
        $orders = $this->orderRepository->findByQuery('some_query');

        // Assert
        $this->assertCount(5, $orders);
    }
}
