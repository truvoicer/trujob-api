<?php

namespace Tests\Unit\Repositories;

use App\Models\TaxRate;
use App\Repositories\TaxRateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxRateRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TaxRateRepository $taxRateRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->taxRateRepository = new TaxRateRepository();
    }

    public function tearDown(): void
    {
        unset($this->taxRateRepository);
        parent::tearDown();
    }

    public function testGetModel(): void
    {
        $model = $this->taxRateRepository->getModel();

        $this->assertInstanceOf(TaxRate::class, $model);
    }

    public function testFindByParams(): void
    {
        // Create some TaxRate models for testing
        TaxRate::factory()->count(3)->create();

        $results = $this->taxRateRepository->findByParams('id', 'asc');

        $this->assertCount(3, $results);
        $this->assertInstanceOf(TaxRate::class, $results->first());

        $resultsLimited = $this->taxRateRepository->findByParams('id', 'asc', 2);
        $this->assertCount(2, $resultsLimited);

        $firstTaxRate = $resultsLimited->first();
        $this->assertNotNull($firstTaxRate);
    }

    public function testFindByQuery(): void
    {
        // Create some TaxRate models for testing
        TaxRate::factory()->count(5)->create();

        $results = $this->taxRateRepository->findByQuery([]);

        $this->assertCount(5, $results);
        $this->assertInstanceOf(TaxRate::class, $results->first());
    }
}
