<?php

namespace App\Services\Tax;

use App\Models\TaxRate;
use App\Services\BaseService;

class TaxRateService extends BaseService
{
    public function createTaxRate(array $data)
    {
        $countryIds = (!empty($data['country_ids']) && is_array($data['country_ids']))
            ? $data['country_ids']
            : [];
        $taxRate = new TaxRate($data);
        if (!$taxRate->save()) {
            throw new \Exception('Error creating tax rate');
        }

        if (count($countryIds)) {
            $taxRate->countries()->sync($data['country_ids']);
        }
        return $taxRate;
    }
    public function updateTaxRate(TaxRate $taxRate, array $data)
    {
        $countryIds = (!empty($data['country_ids']) && is_array($data['country_ids']))
            ? $data['country_ids']
            : [];

        if (!$taxRate->update($data)) {
            throw new \Exception('Error updating tax rate');
        }
        if (count($countryIds)) {
            $taxRate->countries()->sync($data['country_ids']);
        }
        return $taxRate;
    }

    public function deleteTaxRate(TaxRate $taxRate)
    {
        if (!$taxRate->delete()) {
            throw new \Exception('Error deleting tax rate');
        }
        return true;
    }
}
