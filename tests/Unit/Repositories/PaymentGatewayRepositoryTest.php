<?php

namespace Tests\Unit\Repositories;

use App\Models\PaymentGateway;
use App\Repositories\PaymentGatewayRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentGatewayRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentGatewayRepository $paymentGatewayRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentGatewayRepository = new PaymentGatewayRepository();
    }

    protected function tearDown(): void
    {
        unset($this->paymentGatewayRepository);
        parent::tearDown();
    }

    public function testGetModel(): void
    {
        $model = $this->paymentGatewayRepository->getModel();

        $this->assertInstanceOf(PaymentGateway::class, $model);
    }

    public function testFindByParams(): void
    {
        // Create some PaymentGateway records for testing
        PaymentGateway::factory()->count(3)->create();

        $sort = 'name';
        $order = 'asc';
        $count = 2;

        $results = $this->paymentGatewayRepository->findByParams($sort, $order, $count);

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount($count, $results);

        // Optionally, check the sorting if needed.  Example, but needs to be adapted to the actual data
        // $this->assertEquals('value1', $results[0]->name);
    }

    public function testFindByQuery(): void
    {
        // Create some PaymentGateway records for testing
        PaymentGateway::factory()->count(5)->create();

        $results = $this->paymentGatewayRepository->findByQuery('some_query'); // The query doesn't do anything, but the method calls findAll()

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(5, $results);
    }
}