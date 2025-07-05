<?php

namespace App\Services\Shipping;

use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Services\BaseService;
use Illuminate\Support\Str;

class ShippingRateService extends BaseService
{

    public function createShippingRate(ShippingMethod $shippingMethod, array $data)
    {
        $countryIds = [];
        if (isset($data['country_ids']) && is_array($data['country_ids'])) {
            $countryIds = $data['country_ids'];
            unset($data['country_ids']);
        }
        if (empty($data['label'])) {
            throw new \Exception('Shipping rate label is required');
        }
        if (empty($data['name'])) {
            $data['name'] = Str::slug("{$shippingMethod->name}- {$data['label']}", '-');
        }

        $shippingRate = new ShippingRate($data);
        if (!$shippingMethod->rates()->save($shippingRate)) {
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
