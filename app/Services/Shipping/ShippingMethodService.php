<?php

namespace App\Services\Shipping;

use App\Models\ShippingMethod;
use App\Services\BaseService;

class ShippingMethodService extends BaseService
{
    public function createShippingMethod(array $data) {
        $shippingMethod = new ShippingMethod($data);
        if (!$shippingMethod->save()) {
            throw new \Exception('Error creating shipping method');
        }
        return true;
    }
    public function updateShippingMethod(ShippingMethod $shippingMethod, array $data) {
        if (!$shippingMethod->update($data)) {
            throw new \Exception('Error updating shipping method');
        }
        return true;
    }

    public function deleteShippingMethod(ShippingMethod $shippingMethod) {
        if (!$shippingMethod->delete()) {
            throw new \Exception('Error deleting shipping method');
        }
        return true;
    }

    public function syncDiscounts(ShippingMethod $shippingMethod, array $discountIds)
    {
        $shippingMethod->discounts()->sync($discountIds);
        return true;
    }

}
