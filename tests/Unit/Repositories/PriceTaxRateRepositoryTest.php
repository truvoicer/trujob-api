<?php

namespace Tests\Unit\Repositories;

use App\Models\PriceTaxRate;
use App\Repositories\PriceTaxRateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceTaxRateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var PriceTaxRateRepository
     */
    private $priceTaxRateRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->priceTaxRateRepository = new PriceTaxRateRepository();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->priceTaxRateRepository);
    }

    /** @test */
    public function it_can_get_the_model()
    {
        $model = $this->priceTaxRateRepository->getModel();

        $this->assertInstanceOf(PriceTaxRate::class, $model);
    }

    /** @test */
    public function it_can_find_by_params()
    {
        // Arrange
        PriceTaxRate::factory()->count(3)->create();
        $sort = 'name';
        $order = 'asc';
        $count = 2;

        // Act
        $results = $this->priceTaxRateRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $results);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
        foreach($results as $result){
            $this->assertInstanceOf(PriceTaxRate::class, $result);
        }
    }

    /** @test */
    public function it_can_find_by_query()
    {
        // Arrange
        PriceTaxRate::factory()->count(5)->create();

        // Act
        $results = $this->priceTaxRateRepository->findByQuery([]);

        // Assert
        $this->assertCount(5, $results);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
        foreach($results as $result){
            $this->assertInstanceOf(PriceTaxRate::class, $result);
        }
    }
}