<?php

namespace App\Services\Shipping;

use App\Models\ShippingMethod;
use App\Services\BaseService;
use Illuminate\Support\Str;

class ShippingMethodService extends BaseService
{
    public function createShippingMethod(array $data)
    {
        $rates = $data['rates'] ?? [];
        unset($data['rates']);
        
        $data['name'] = Str::slug($data['carrier']);
        $shippingMethod = new ShippingMethod($data);
        if (!$shippingMethod->save()) {
            throw new \Exception('Error creating shipping method');
        }
        $this->saveShippingRates($shippingMethod, $rates);
        return true;
    }
    public function updateShippingMethod(ShippingMethod $shippingMethod, array $data)
    {
        $rates = $data['rates'] ?? [];
        unset($data['rates']);
        if (!empty($data['carrier'])) {
            $data['name'] = Str::slug($data['carrier']);
        }
        if (!$shippingMethod->update($data)) {
            throw new \Exception('Error updating shipping method');
        }
        $this->saveShippingRates($shippingMethod, $rates);
        return true;
    }

    public function deleteShippingMethod(ShippingMethod $shippingMethod)
    {
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

    public function saveShippingRates(ShippingMethod $shippingMethod, array $rates)
    {
        foreach ($rates as $rate) {
            if (!empty($rate['id'])) {
                $shippingRate = $shippingMethod->rates()->find($rate['id']);
                if (!$shippingRate) {
                    throw new \Exception("Shipping rate not found for ID: {$rate['id']}");
                }
                $shippingRate->update($rate);
            } else {
                $rate['shipping_method_id'] = $shippingMethod->id;
                $shippingMethod->rates()->create($rate);
            }
        }
        return true;
    }
}
