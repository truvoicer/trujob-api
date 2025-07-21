<?php

namespace Tests\Unit\Services\Product;

use App\Enums\Price\PriceType;
use App\Models\Product;
use App\Models\Price;
use App\Models\TaxRate;
use App\Models\Discount;
use App\Models\Currency;
use App\Models\Country;
use App\Services\Product\ProductPriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductPriceService $productPriceService;
    private Product $product;
    private array $priceData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productPriceService = new ProductPriceService();
        $this->product = Product::factory()->create();
        $currency = Currency::factory()->create();
        $country = Country::factory()->create();
        $this->priceData = [
            'price_type' => PriceType::ONE_TIME->value,
            'value' => 100,
            'currency_id' => $currency->id,
            'country_id' => $country->id,
        ];
    }

    public function test_createproductPrice_creates_price_successfully()
    {
        $data = $this->priceData;
        $this->assertTrue($this->productPriceService->createproductPrice($this->product, $data));
        $this->assertCount(1, $this->product->prices);
    }

    public function test_createproductPrice_throws_exception_if_price_already_exists()
    {
        $this->expectException(\Exception::class);
        $data = $this->priceData;
        $this->productPriceService->createproductPrice($this->product, $data);
        $this->productPriceService->createproductPrice($this->product, $data);
    }

    public function test_updateproductPrice_updates_price_successfully()
    {
        $price = Price::factory()->create($this->priceData);
        $this->product->prices()->attach($price->id);
        $updatedData = ['value' => 200];

        $this->assertTrue($this->productPriceService->updateproductPrice($this->product, $price, $updatedData));
        $this->assertEquals(200, $price->refresh()->value);
    }

    public function test_updateproductPrice_throws_exception_if_price_not_found()
    {
        $this->expectException(\Exception::class);
        $price = Price::factory()->make(['id' => 999]);
        $this->productPriceService->updateproductPrice($this->product, $price, ['value' => 200]);
    }

    public function test_syncTaxRateIds_syncs_tax_rates()
    {
        $price = Price::factory()->create();
        $taxRate1 = TaxRate::factory()->create();
        $taxRate2 = TaxRate::factory()->create();

        $this->productPriceService->syncTaxRateIds($price, [$taxRate1->id, $taxRate2->id]);

        $this->assertCount(2, $price->taxRates);
    }

    public function test_syncDiscounts_syncs_discounts()
    {
        $price = Price::factory()->create();
        $discount1 = Discount::factory()->create();
        $discount2 = Discount::factory()->create();

        $this->productPriceService->syncDiscounts($price, [$discount1->id, $discount2->id]);

        $this->assertCount(2, $price->discounts);
    }

    public function test_deleteproductPrice_deletes_price_successfully()
    {
        $price = Price::factory()->create($this->priceData);
        $this->product->prices()->attach($price->id);

        $this->assertTrue($this->productPriceService->deleteproductPrice($this->product, $price));
        $this->assertCount(0, $this->product->prices);
    }

    public function test_deleteproductPrice_throws_exception_if_price_not_found()
    {
        $this->expectException(\Exception::class);
        $price = Price::factory()->make(['id' => 999]);
        $this->productPriceService->deleteproductPrice($this->product, $price);
    }

    public function test_attachBulkPricesToProduct_attaches_prices_successfully()
    {
        $price1 = Price::factory()->create();
        $price2 = Price::factory()->create();

        $this->assertTrue($this->productPriceService->attachBulkPricesToProduct($this->product, [$price1->id, $price2->id]));
        $this->assertCount(2, $this->product->prices);
    }

    public function test_detachBulkPricesFromProduct_detaches_prices_successfully()
    {
        $price1 = Price::factory()->create();
        $price2 = Price::factory()->create();
        $this->product->prices()->attach([$price1->id, $price2->id]);

        $this->assertTrue($this->productPriceService->detachBulkPricesFromProduct($this->product, [$price1->id, $price2->id]));
        $this->assertCount(0, $this->product->prices);
    }
}