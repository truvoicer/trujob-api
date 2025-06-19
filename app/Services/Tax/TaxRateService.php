<?php

namespace App\Services\Tax;

use App\Enums\Order\Tax\TaxRateLocaleType;
use App\Factories\Tax\TaxRateLocaleFactory;
use App\Models\TaxRate;
use App\Services\BaseService;
use Illuminate\Support\Str;

class TaxRateService extends BaseService
{
    public function createTaxRate(array $data)
    {
        $locales = $data['locales'] ?? null;
        if (isset($data['locales'])) {
            unset($data['locales']);
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

        if (is_array($locales) && count($locales) > 0) {
            $this->syncTaxRateLocale($taxRate, $locales);
        }

        return $taxRate;
    }
    public function syncTaxRateLocale(TaxRate $taxRate, array $data): void
    {
        $groupedData = collect($data)->groupBy('localeable_type');
        foreach ($groupedData as $localeableType => $locales) {
            $locales = $locales->toArray();
            $taxRate->locales()->where('localeable_type', $localeableType)
                ->whereNotIn('localeable_id', array_column($locales, 'localeable_id'))
                ->delete();
            foreach ($locales as $locale) {
                TaxRateLocaleFactory::create(TaxRateLocaleType::tryFrom($localeableType))
                    ->attachTaxRateLocale($taxRate, $locale);
            }
        }

    }
    public function updateTaxRate(TaxRate $taxRate, array $data)
    {
        $locales = $data['locales'] ?? null;
        if (isset($data['locales'])) {
            unset($data['locales']);
        }
        if (!empty($data['has_region']) && empty($data['region_id'])) {
            throw new \Exception('Region ID is required when has_region is true');
        }

        if (!$taxRate->update($data)) {
            throw new \Exception('Error updating tax rate');
        }

        $this->updateDefaultTaxRate($taxRate, $data);

        if (is_array($locales) && count($locales) > 0) {
            $this->syncTaxRateLocale($taxRate, $locales);
        }

        return $taxRate;
    }

    public function updateDefaultTaxRate(TaxRate $taxRate, array $data): void
    {
        if (array_key_exists('is_default', $data)  && $data['is_default']) {
            $taxRate->default()->create();
        } else if (array_key_exists('is_default', $data) && !$data['is_default']) {
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
