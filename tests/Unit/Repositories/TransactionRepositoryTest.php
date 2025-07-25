<?php

namespace Tests\Unit\Repositories;

use App\Models\Currency;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\TransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionRepository $transactionRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionRepository = new TransactionRepository();
    }

    public function testGetModelReturnsCorrectModel(): void
    {
        $model = $this->transactionRepository->getModel();
        $this->assertInstanceOf(Transaction::class, $model);
    }

    public function testFindByParamsReturnsCollection(): void
    {
        $paymentGateway = PaymentGateway::factory()->create();
        $currency = Currency::factory()->create();
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);
        Transaction::factory()->count(3)->create([
            'order_id' => $order->id,
            'payment_gateway_id' => $paymentGateway->id,
        ]);

        $result = $this->transactionRepository->findByParams('id', 'asc');

        $this->assertCount(3, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function testFindByParamsReturnsCollectionWithCountLimit(): void
    {
        $paymentGateway = PaymentGateway::factory()->create();
        $currency = Currency::factory()->create();
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);
        Transaction::factory()->count(5)->create([
            'order_id' => $order->id,
            'payment_gateway_id' => $paymentGateway->id,
        ]);

        $result = $this->transactionRepository->findByParams('id', 'asc', 2);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    public function testFindByQueryReturnsAllTransactions(): void
    {
        $paymentGateway = PaymentGateway::factory()->create();
        $currency = Currency::factory()->create();
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);
        Transaction::factory()->count(2)->create([
            'order_id' => $order->id,
            'payment_gateway_id' => $paymentGateway->id,
        ]);

        $result = $this->transactionRepository->findByQuery([]);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }
}
