<?php

namespace App\Services\Tax;

use App\Models\TaxRate;
use App\Services\BaseService;

class TaxRateService extends BaseService
{
    public function createTaxRate(array $data)
    {
        if (!empty($data['has_region']) && empty($data['region_id'])) {
            throw new \Exception('Region ID is required when has_region is true');
        }
        if (empty($data['has_region'])) {
            $data['region_id'] = null;
        }
        $taxRate = new TaxRate($data);
        if (!$taxRate->save()) {
            throw new \Exception('Error creating tax rate');
        }

        $this->updateDefaultTaxRate($taxRate, $data);

        return $taxRate;
    }
    public function updateTaxRate(TaxRate $taxRate, array $data)
    {
        if (!empty($data['has_region']) && empty($data['region_id'])) {
            throw new \Exception('Region ID is required when has_region is true');
        }
        if (empty($data['has_region'])) {
            $data['region_id'] = null;
        }
        if (!$taxRate->update($data)) {
            throw new \Exception('Error updating tax rate');
        }

        $this->updateDefaultTaxRate($taxRate, $data);

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
