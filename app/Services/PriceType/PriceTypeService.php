<?php
namespace App\Services\PriceType;

use App\Models\PriceType;
use App\Services\BaseService;

class PriceTypeService extends BaseService
{
    // public function calculateFinalPriceType(): float
    // {
    //     $discountedPriceType = $this->priceType - ($this->priceType * ($this->discount / 100));
    //     $finalPriceType = $discountedPriceType + ($discountedPriceType * ($this->tax / 100));
    //     return round($finalPriceType, 2);
    // }

    public function createPriceType(array $data): bool
    {
        $priceType = new PriceType($data);
        if (!$priceType->save()) {
            throw new \Exception('Error creating priceType');
        }
        return true;
    }

    public function updatePriceType(PriceType $priceType, array $data): bool
    {
        if (!$priceType->update($data)) {
            throw new \Exception('Error updating priceType');
        }
        return true;
    }
    public function deletePriceType(PriceType $priceType): bool
    {
        if (!$priceType->delete()) {
            throw new \Exception('Error deleting priceType');
        }
        return true;
    }

}