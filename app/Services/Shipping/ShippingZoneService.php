<?php

namespace App\Services\Shipping;

use App\Models\ShippingZone;
use App\Services\BaseService;

class ShippingZoneService extends BaseService
{
    public function createShippingZone(array $data)
    {
        $countryIds = [];
        if (isset($data['country_ids']) && is_array($data['country_ids'])) {
            $countryIds = $data['country_ids'];
            unset($data['country_ids']);
        }
        $shippingZone = new ShippingZone($data);
        if (!$shippingZone->save()) {
            throw new \Exception('Error creating shipping zone');
        }
        $shippingZone->refresh();
        if (count($countryIds)) {
            $shippingZone->countries()->sync($countryIds);
        }
        return true;
    }
    public function updateShippingZone(ShippingZone $shippingZone, array $data)
    {
        $countryIds = [];
        if (isset($data['country_ids']) && is_array($data['country_ids'])) {
            $countryIds = $data['country_ids'];
            unset($data['country_ids']);
        }
        if (!$shippingZone->update($data)) {
            throw new \Exception('Error updating shipping zone');
        }
        $shippingZone->refresh();
        if (count($countryIds)) {
            $shippingZone->countries()->sync($countryIds);
        }
        return true;
    }

    public function deleteShippingZone(ShippingZone $shippingZone)
    {
        if (!$shippingZone->delete()) {
            throw new \Exception('Error deleting shipping zone');
        }
        return true;
    }

    public function syncCountries(ShippingZone $shippingZone, array $countryIds)
    {
        $shippingZone->countries()->sync($countryIds);
        return true;
    }

    public function syncDiscounts(ShippingZone $shippingZone, array $discountIds)
    {
        $shippingZone->discounts()->sync($discountIds);
        return true;
    }
}
