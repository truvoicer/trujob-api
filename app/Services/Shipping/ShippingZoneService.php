<?php

namespace App\Services\Shipping;

use App\Enums\Order\Shipping\ShippingZoneAbleType;
use App\Factories\Shipping\ShippingZoneAbleFactory;
use App\Models\ShippingZone;
use App\Services\BaseService;

class ShippingZoneService extends BaseService
{
    public function syncShippingZoneAbles(ShippingZone $shippingZone, array $data): void
    {
        $groupedData = collect($data)->groupBy('shipping_zoneable_type');
        foreach ($groupedData as $tax_rateableType => $shippingZoneables) {
            $shippingZoneables = $shippingZoneables->toArray();
            $shippingZone->shippingZoneAbles()->where('shipping_zoneable_type', $tax_rateableType)
                ->whereNotIn('shipping_zoneable_id', array_column($shippingZoneables, 'shipping_zoneable_id'))
                ->delete();
            foreach ($shippingZoneables as $locale) {
                ShippingZoneAbleFactory::create(ShippingZoneAbleType::tryFrom($tax_rateableType))
                    ->attachShippingZoneAble($shippingZone, $locale);
            }
        }
    }
    public function createShippingZone(array $data)
    {

        $shippingZoneables = $data['shipping_zoneables'] ?? null;
        if (isset($data['shipping_zoneables'])) {
            unset($data['shipping_zoneables']);
        }
        $shippingZone = new ShippingZone($data);
        if (!$shippingZone->save()) {
            throw new \Exception('Error creating shipping zone');
        }
        $shippingZone->refresh();
        if (empty($data['all']) && is_array($shippingZoneables) && count($shippingZoneables) > 0) {
            $this->syncShippingZoneAbles($shippingZone, $shippingZoneables);
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
        if (empty($data['all']) && count($countryIds)) {
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

    public function destroyBulkShippingZones(array $ids)
    {
        $shippingZones = ShippingZone::whereIn('id', $ids)->get();
        foreach ($shippingZones as $shippingZone) {
            if (!$this->deleteShippingZone($shippingZone)) {
                return false;
            }
        }
        return true;
    }
}
