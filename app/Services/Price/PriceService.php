<?php
namespace App\Services\Price;

use App\Models\Price;
use App\Services\BaseService;

class PriceService extends BaseService
{
    // public function calculateFinalPrice(): float
    // {
    //     $discountedPrice = $this->price - ($this->price * ($this->discount / 100));
    //     $finalPrice = $discountedPrice + ($discountedPrice * ($this->tax / 100));
    //     return round($finalPrice, 2);
    // }

    public function createPrice(array $data): bool
    {
        if (empty($data['created_by_user_id'])) {
            $data['created_by_user_id'] = request()->user()->id ?? null;
        }
        $price = new Price($data);
        if (!$price->save()) {
            throw new \Exception('Error creating price');
        }
        return true;
    }

    public function updatePrice(Price $price, array $data): bool
    {
        if (empty($data['created_by_user_id'])) {
            $data['created_by_user_id'] = request()->user()->id ?? null;
        }
        if (!$price->update($data)) {
            throw new \Exception('Error updating price');
        }
        return true;
    }
    public function deletePrice(Price $price): bool
    {
        if (!$price->delete()) {
            throw new \Exception('Error deleting price');
        }
        return true;
    }

}