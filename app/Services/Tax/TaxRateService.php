<?php

namespace App\Services\Tax;

use App\Models\TaxRate;
use App\Services\BaseService;

class TaxRateService extends BaseService
{
    public function createTaxRate(array $data)
    {
        $taxRate = new TaxRate($data);
        if (!$taxRate->save()) {
            throw new \Exception('Error creating tax rate');
        }

        return $taxRate;
    }
    public function updateTaxRate(TaxRate $taxRate, array $data)
    {
        if (!$taxRate->update($data)) {
            throw new \Exception('Error updating tax rate');
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
