<?php

namespace Tests\Unit\Repositories;

use App\Enums\Order\OrderItemType;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
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


    public function test_it_can_get_the_model(): void
    {
        $model = $this->orderItemRepository->getModel();

        $this->assertInstanceOf(OrderItem::class, $model);
    }


    public function test_it_can_find_by_params(): void
    {
        // Arrange

        $user = User::factory()->create();

        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);
        $currency = Currency::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);
        OrderItem::factory()->count(3)->create([
            'order_id' => $order->id,
            'order_itemable_id' => $product->id,
            'order_itemable_type' => OrderItemType::PRODUCT->value,
        ]);
        $sort = 'id';
        $order = 'asc';

        // Act
        $result = $this->orderItemRepository->findByParams($sort, $order);

        // Assert
        $this->assertCount(3, $result);
        $this->assertInstanceOf(OrderItem::class, $result->first());
        $this->assertEquals(1, $result->first()->id);
    }


    public function test_it_can_find_by_query(): void
    {
        // Arrange
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);
        $currency = Currency::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);
        OrderItem::factory()->count(2)->create([
            'order_id' => $order->id,
            'order_itemable_id' => $product->id,
            'order_itemable_type' => OrderItemType::PRODUCT->value,
        ]);

        // Act
        $result = $this->orderItemRepository->findByQuery([]);

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(OrderItem::class, $result->first());
    }
}
