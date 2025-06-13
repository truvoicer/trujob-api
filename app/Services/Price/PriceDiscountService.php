<?php
namespace App\Services\Price;

use App\Models\Price;
use App\Services\BaseService;

class PriceDiscountService extends BaseService
{
    public function attachBulkDiscountsToPrice(Price $price, array $discountIds): bool
    {
        $result = $price->discounts()->syncWithoutDetaching($discountIds);
        return !empty($result['attached']);
    }
    public function detachBulkDiscountsFromPrice(Price $price, array $discountIds): bool
    {
        $result = $price->discounts()->detach($discountIds);
        return !empty($result);
    }
}
