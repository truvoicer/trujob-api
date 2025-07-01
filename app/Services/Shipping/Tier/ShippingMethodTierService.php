<?php

namespace App\Services\Shipping\Tier;

use App\Models\ShippingMethod;
use App\Models\ShippingMethodTier;
use App\Services\BaseService;
use Illuminate\Support\Str;

class ShippingMethodTierService extends BaseService
{
    public function createShippingMethodTier(ShippingMethod $shippingMethod, array $data)
    {
        $data['name'] = Str::slug($data['label']);
        if (!$shippingMethod->tiers()->create($data)) {
            throw new \Exception('Error creating shipping method');
        }

        return true;
    }
    public function updateShippingMethodTier(ShippingMethodTier $shippingMethodTier, array $data)
    {
        if (!empty($data['label'])) {
            $data['name'] = Str::slug($data['label']);
        }
        if (!$shippingMethodTier->update($data)) {
            throw new \Exception('Error updating shipping method');
        }
        return true;
    }

    public function deleteShippingMethodTier(ShippingMethodTier $shippingMethodTier)
    {
        if (!$shippingMethodTier->delete()) {
            throw new \Exception('Error deleting shipping method');
        }
        return true;
    }

}
