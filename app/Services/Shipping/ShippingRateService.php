<?php

namespace App\Services\Shipping;

use App\Models\ShippingRate;
use App\Services\BaseService;

class ShippingRateService extends BaseService
{
    public function createShippingRate(array $data)
    {
        $countryIds = [];
        if (isset($data['country_ids']) && is_array($data['country_ids'])) {
            $countryIds = $data['country_ids'];
            unset($data['country_ids']);
        }
        $shippingRate = new ShippingRate($data);
        if (!$shippingRate->save()) {
            throw new \Exception('Error creating shipping rate');
        }
        $shippingRate->refresh();
        if (count($countryIds)) {
            $shippingRate->countries()->sync($countryIds);
        }
        return true;
    }
    public function updateShippingRate(ShippingRate $shippingRate, array $data)
    {
        $countryIds = [];
        if (isset($data['country_ids']) && is_array($data['country_ids'])) {
            $countryIds = $data['country_ids'];
            unset($data['country_ids']);
        }
        if (!$shippingRate->update($data)) {
            throw new \Exception('Error updating shipping rate');
        }
        $shippingRate->refresh();
        if (count($countryIds)) {
            $shippingRate->countries()->sync($countryIds);
        }
        return true;
    }

    public function deleteShippingRate(ShippingRate $shippingRate)
    {
        if (!$shippingRate->delete()) {
            throw new \Exception('Error deleting shipping rate');
        }
        return true;
    }

    public function destroyBulkShippingRates(array $ids)
    {
        $shippingRates = ShippingRate::whereIn('id', $ids)->get();
        foreach ($shippingRates as $shippingRate) {
            if (!$this->deleteShippingRate($shippingRate)) {
                return false;
            }
        }
        return true;
    }
}
