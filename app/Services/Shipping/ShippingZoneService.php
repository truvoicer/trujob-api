<?php

namespace App\Services\Shipping;

use App\Enums\Order\Shipping\ShippingZoneAbleType;
use App\Factories\Shipping\ShippingZoneAbleFactory;
use App\Models\ShippingZone;
use App\Services\BaseService;
use Illuminate\Support\Str;

class ShippingZoneService extends BaseService
{
    public function syncShippingZoneAbles(ShippingZone $shippingZone, array $data): void
    {
        $groupedData = collect($data)->groupBy('shipping_zoneable_type');
        foreach (ShippingZoneAbleType::cases() as $shippingZoneAbleType) {
            $shippingZoneAbles = $groupedData->get($shippingZoneAbleType->value, collect())->toArray();
            ShippingZoneAbleFactory::create($shippingZoneAbleType)
                ->syncShippingZoneAble($shippingZone, $shippingZoneAbles);
        }
    }
    public function createShippingZone(array $data)
    {

        $shippingZoneAbles = $data['shipping_zoneables'] ?? null;
        if (isset($data['shipping_zoneables'])) {
            unset($data['shipping_zoneables']);
        }

        if (isset($data['label'])) {
            $data['name'] = Str::slug($data['label']);
        }
        $shippingZone = new ShippingZone($data);
        if (!$shippingZone->save()) {
            throw new \Exception('Error creating shipping zone');
        }
        $shippingZone->refresh();
        if (empty($data['all']) && is_array($shippingZoneAbles) && count($shippingZoneAbles) > 0) {
            $this->syncShippingZoneAbles($shippingZone, $shippingZoneAbles);
        }
        return true;
    }
    public function updateShippingZone(ShippingZone $shippingZone, array $data)
    {

        $shippingZoneAbles = $data['shipping_zoneables'] ?? null;
        if (isset($data['shipping_zoneables'])) {
            unset($data['shipping_zoneables']);
        }
        if (isset($data['label'])) {
            $data['name'] = Str::slug($data['label']);
        }
        if (!$shippingZone->update($data)) {
            throw new \Exception('Error updating shipping zone');
        }
        $shippingZone->refresh();
        if (empty($data['all']) && is_array($shippingZoneAbles) && count($shippingZoneAbles) > 0) {
            $this->syncShippingZoneAbles($shippingZone, $shippingZoneAbles);
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
