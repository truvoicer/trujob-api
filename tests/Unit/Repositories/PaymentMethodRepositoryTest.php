<?php

namespace Tests\Unit\Repositories;

use App\Models\PaymentMethod;
use App\Repositories\PaymentMethodRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PaymentMethodRepository $paymentMethodRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentMethodRepository = new PaymentMethodRepository();
    }

    /** @test */
    public function it_can_get_the_payment_method_model()
    {
        $model = $this->paymentMethodRepository->getModel();

        $this->assertInstanceOf(PaymentMethod::class, $model);
    }

    /** @test */
    public function it_can_find_payment_methods_by_params()
    {
        // Arrange
        PaymentMethod::factory()->count(3)->create();
        $sort = 'name';
        $order = 'asc';
        $count = 2;

        // Act
        $result = $this->paymentMethodRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result); // Check for collection instance
    }

    /** @test */
    public function it_can_find_payment_methods_by_query()
    {
        // Arrange
        PaymentMethod::factory()->count(5)->create();

        // Act
        $result = $this->paymentMethodRepository->findByQuery('test'); // Dummy query

        // Assert
        $this->assertCount(5, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result); // Check for collection instance
    }
}