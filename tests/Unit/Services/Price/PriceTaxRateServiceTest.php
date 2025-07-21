<?php

namespace Tests\Unit\Services\Price;

use App\Models\Price;
use App\Models\PriceTaxRate;
use App\Models\TaxRate;
use App\Services\Price\PriceTaxRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceTaxRateServiceTest extends TestCase
{
    use RefreshDatabase;

    private PriceTaxRateService $priceTaxRateService;
    private Price $price;
    private TaxRate $taxRate;
    private array $taxRateIds;

    protected function setUp(): void
    {
        parent::setUp();
        $this->priceTaxRateService = new PriceTaxRateService();
        $this->price = Price::factory()->create();
        $this->taxRate = TaxRate::factory()->create();
        $this->taxRateIds = [$this->taxRate->id];
    }

    public function test_attach_bulk_tax_rates_to_price(): void
    {
        $result = $this->priceTaxRateService->attachBulkTaxRatesToPrice($this->price, $this->taxRateIds);

        $this->assertTrue($result);
        $this->assertDatabaseHas('price_tax_rate', [
            'price_id' => $this->price->id,
            'tax_rate_id' => $this->taxRate->id,
        ]);
    }

    public function test_detach_bulk_tax_rates_from_price(): void
    {
        $this->price->taxRates()->attach($this->taxRateIds);

        $result = $this->priceTaxRateService->detachBulkTaxRatesFromPrice($this->price, $this->taxRateIds);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('price_tax_rate', [
            'price_id' => $this->price->id,
            'tax_rate_id' => $this->taxRate->id,
        ]);
    }

    public function test_create_price_tax_rate(): void
    {
        $result = $this->priceTaxRateService->createPriceTaxRate($this->price, $this->taxRateIds);

        $this->assertArrayHasKey('attached', $result);
        $this->assertArrayHasKey('detached', $result);
        $this->assertArrayHasKey('updated', $result);

        $this->assertDatabaseHas('price_tax_rate', [
            'price_id' => $this->price->id,
            'tax_rate_id' => $this->taxRate->id,
        ]);
    }

    public function test_update_price_tax_rate(): void
    {
        $this->price->taxRates()->attach($this->taxRateIds);
        $data = ['tax_rate' => 0.1];

        $result = $this->priceTaxRateService->updatePriceTaxRate($this->price, $this->taxRate, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('price_tax_rate', [
            'price_id' => $this->price->id,
            'tax_rate_id' => $this->taxRate->id,
        ]);
    }

    public function test_update_price_tax_rate_throws_exception_if_not_found(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Price tax rate does not exist for this price');

        $data = ['tax_rate' => 0.1];
        $this->priceTaxRateService->updatePriceTaxRate($this->price, $this->taxRate, $data);
    }


    public function test_delete_price_tax_rate(): void
    {
        $this->price->taxRates()->attach($this->taxRateIds);

        $result = $this->priceTaxRateService->deletePriceTaxRate($this->price, $this->taxRate);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('price_tax_rate', [
            'price_id' => $this->price->id,
            'tax_rate_id' => $this->taxRate->id,
        ]);
    }

    public function test_delete_price_tax_rate_throws_exception_if_not_found(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Price tax rate does not exist for this price');

        $this->priceTaxRateService->deletePriceTaxRate($this->price, $this->taxRate);
    }
}
