<?php
namespace App\Services\Price;

use App\Models\Price;
use App\Models\PriceTaxRate;
use App\Models\TaxRate;
use App\Services\BaseService;

class PriceTaxRateService extends BaseService
{
    // public function calculateFinalPriceTaxRate(): float
    // {
    //     $discountedPriceTaxRate = $this->priceTaxRate - ($this->priceTaxRate * ($this->discount / 100));
    //     $finalPriceTaxRate = $discountedPriceTaxRate + ($discountedPriceTaxRate * ($this->tax / 100));
    //     return round($finalPriceTaxRate, 2);
    // }

    public function attachBulkTaxRatesToPrice(Price $price, array $taxRateIds): bool
    {
        $result = $price->taxRates()->syncWithoutDetaching($taxRateIds);
        return true;
    }
    public function detachBulkTaxRatesFromPrice(Price $price, array $taxRateIds): bool
    {
        $result = $price->taxRates()->detach($taxRateIds);
        return true;
    }

    public function createPriceTaxRate(Price $price, array $taxRateIds): array
    {
        return $price->taxRates()->sync($taxRateIds);
    }

    public function updatePriceTaxRate(Price $price, TaxRate $taxRate, array $data): bool
    {
        $priceTaxRate = $price->taxRates()->where('id', $taxRate->id)->first();
        if (!$priceTaxRate) {
            throw new \Exception('Price tax rate does not exist for this price');
        }

        $priceTaxRate = $price->taxRates()->updateExistingPivot(
            $taxRate->id,
            $data
        );
        return true;
    }


    public function deletePriceTaxRate(Price $price, TaxRate $taxRate): bool
    {
        $check = $price->taxRates()->where('id', $taxRate->id)->exists();
        if (!$check) {
            throw new \Exception('Price tax rate does not exist for this price');
        }
        $price->taxRates()->detach($taxRate->id);
        return true;
    }

}
