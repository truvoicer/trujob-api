<?php

namespace Tests\Unit\Services\Tax;

use App\Enums\Order\Tax\TaxRateAbleType;
use App\Factories\Tax\TaxRateAbleFactory;
use App\Models\TaxRate;
use App\Models\TaxRateDefault;
use App\Services\Tax\TaxRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class TaxRateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaxRateService $taxRateService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxRateService = new TaxRateService();
    }

    public function testCreateTaxRate(): void
    {
        $data = [
            'label' => 'Test Tax Rate',
            'rate' => 0.10,
            'is_default' => true,
            'tax_rateables' => [
                [
                    'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
                    'tax_rateable_id' => 1,
                ],
            ],
        ];

        $taxRate = $this->taxRateService->createTaxRate($data);

        $this->assertInstanceOf(TaxRate::class, $taxRate);
        $this->assertEquals('test-tax-rate', $taxRate->name);
        $this->assertEquals(0.10, $taxRate->rate);
        $this->assertDatabaseHas('tax_rates', ['name' => 'test-tax-rate', 'rate' => 0.10]);
        $this->assertDatabaseHas('tax_rate_defaults', ['tax_rate_id' => $taxRate->id]);
        $this->assertDatabaseHas('tax_rate_ables', [
            'tax_rate_id' => $taxRate->id,
            'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
            'tax_rateable_id' => 1,
        ]);

    }

    public function testSyncTaxRateAble(): void
    {
        $taxRate = TaxRate::factory()->create();
        $data = [
            [
                'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
                'tax_rateable_id' => 1,
            ],
            [
                'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
                'tax_rateable_id' => 2,
            ],
        ];

        $this->taxRateService->syncTaxRateAble($taxRate, $data);

        $this->assertDatabaseHas('tax_rate_ables', [
            'tax_rate_id' => $taxRate->id,
            'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
            'tax_rateable_id' => 1,
        ]);
        $this->assertDatabaseHas('tax_rate_ables', [
            'tax_rate_id' => $taxRate->id,
            'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
            'tax_rateable_id' => 2,
        ]);

        //Sync again to remove one of the taxables
        $data = [
            [
                'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
                'tax_rateable_id' => 1,
            ],
        ];

        $this->taxRateService->syncTaxRateAble($taxRate, $data);

        $this->assertDatabaseHas('tax_rate_ables', [
            'tax_rate_id' => $taxRate->id,
            'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
            'tax_rateable_id' => 1,
        ]);

        $this->assertDatabaseMissing('tax_rate_ables', [
            'tax_rate_id' => $taxRate->id,
            'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
            'tax_rateable_id' => 2,
        ]);
    }

    public function testUpdateTaxRate(): void
    {
        $taxRate = TaxRate::factory()->create(['rate' => 0.10]);

        $data = [
            'rate' => 0.20,
            'is_default' => true,
            'tax_rateables' => [
                [
                    'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
                    'tax_rateable_id' => 1,
                ],
            ],
        ];

        $updatedTaxRate = $this->taxRateService->updateTaxRate($taxRate, $data);

        $this->assertEquals(0.20, $updatedTaxRate->rate);
        $this->assertDatabaseHas('tax_rates', ['id' => $taxRate->id, 'rate' => 0.20]);
        $this->assertDatabaseHas('tax_rate_defaults', ['tax_rate_id' => $taxRate->id]);
        $this->assertDatabaseHas('tax_rate_ables', [
            'tax_rate_id' => $taxRate->id,
            'tax_rateable_type' => TaxRateAbleType::PRODUCT->value,
            'tax_rateable_id' => 1,
        ]);
    }

    public function testSetAsDefault(): void
    {
        $taxRate = TaxRate::factory()->create();

        $this->taxRateService->setAsDefault($taxRate);

        $this->assertDatabaseHas('tax_rate_defaults', ['tax_rate_id' => $taxRate->id]);
    }

    public function testRemoveAsDefault(): void
    {
        $taxRate = TaxRate::factory()->create();
        $taxRate->default()->create();

        $this->taxRateService->removeAsDefault($taxRate);

        $this->assertDatabaseMissing('tax_rate_defaults', ['tax_rate_id' => $taxRate->id]);
    }

    public function testDeleteTaxRate(): void
    {
        $taxRate = TaxRate::factory()->create();

        $this->taxRateService->deleteTaxRate($taxRate);

        $this->assertDatabaseMissing('tax_rates', ['id' => $taxRate->id]);
    }

    public function testDestroyBulkTaxRates(): void
    {
        $taxRate1 = TaxRate::factory()->create();
        $taxRate2 = TaxRate::factory()->create();
        $ids = [$taxRate1->id, $taxRate2->id];

        $result = $this->taxRateService->destroyBulkTaxRates($ids);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tax_rates', ['id' => $taxRate1->id]);
        $this->assertDatabaseMissing('tax_rates', ['id' => $taxRate2->id]);
    }
}
