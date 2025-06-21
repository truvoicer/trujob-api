<?php

namespace App\Services\Tax;

use App\Enums\Order\Tax\TaxRateAbleType;
use App\Factories\Tax\TaxRateAbleFactory;
use App\Models\TaxRate;
use App\Services\BaseService;
use Illuminate\Support\Str;

class TaxRateService extends BaseService
{
    public function createTaxRate(array $data)
    {
        $taxRateables = $data['tax_rateables'] ?? null;
        if (isset($data['tax_rateables'])) {
            unset($data['tax_rateables']);
        }

        if (!empty($data['label'])) {
            $data['name'] = Str::slug($data['label']);
        }
        if (!empty($data['has_region']) && empty($data['region_id'])) {
            throw new \Exception('Region ID is required when has_region is true');
        }
        $taxRate = new TaxRate($data);
        if (!$taxRate->save()) {
            throw new \Exception('Error creating tax rate');
        }

        $this->updateDefaultTaxRate($taxRate, $data);

        if (is_array($taxRateables) && count($taxRateables) > 0) {
            $this->syncTaxRateAble($taxRate, $taxRateables);
        }

        return $taxRate;
    }
    public function syncTaxRateAble(TaxRate $taxRate, array $data): void
    {
        $groupedData = collect($data)->groupBy('tax_rateable_type');
        foreach ($groupedData as $tax_rateableType => $taxRateables) {
            $taxRateables = $taxRateables->toArray();
            $taxRate->taxRateAbles()->where('tax_rateable_type', $tax_rateableType)
                ->whereNotIn('tax_rateable_id', array_column($taxRateables, 'tax_rateable_id'))
                ->delete();
            foreach ($taxRateables as $locale) {
                TaxRateAbleFactory::create(TaxRateAbleType::tryFrom($tax_rateableType))
                    ->attachTaxRateAble($taxRate, $locale);
            }
        }

    }
    public function updateTaxRate(TaxRate $taxRate, array $data)
    {
        $taxRateables = $data['tax_rateables'] ?? null;
        if (isset($data['tax_rateables'])) {
            unset($data['tax_rateables']);
        }
        if (!empty($data['has_region']) && empty($data['region_id'])) {
            throw new \Exception('Region ID is required when has_region is true');
        }

        if (!$taxRate->update($data)) {
            throw new \Exception('Error updating tax rate');
        }

        $this->updateDefaultTaxRate($taxRate, $data);

        if (is_array($taxRateables) && count($taxRateables) > 0) {
            $this->syncTaxRateAble($taxRate, $taxRateables);
        }

        return $taxRate;
    }

    public function updateDefaultTaxRate(TaxRate $taxRate, array $data): void
    {
        if (array_key_exists('is_default', $data)  && $data['is_default']) {
            $this->setAsDefault($taxRate);
        } else if (array_key_exists('is_default', $data) && !$data['is_default']) {
            $this->removeAsDefault($taxRate);
        }
    }

    public function setAsDefault(TaxRate $taxRate): void
    {
        if (!$taxRate->default) {
            $taxRate->default()->create();
        }
    }
    public function removeAsDefault(TaxRate $taxRate): void
    {
        if ($taxRate->default) {
            $taxRate->default()->delete();
        }
    }
    public function deleteTaxRate(TaxRate $taxRate)
    {
        if (!$taxRate->delete()) {
            throw new \Exception('Error deleting tax rate');
        }
        return true;
    }

    public function destroyBulkTaxRates(array $ids): bool
    {
        $taxRates = TaxRate::whereIn('id', $ids)->get();
        foreach ($taxRates as $taxRate) {
            if (!$this->deleteTaxRate($taxRate)) {
                return false;
            }
        }
        return true;
    }
}
