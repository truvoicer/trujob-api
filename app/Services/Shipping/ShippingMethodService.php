<?php

namespace App\Services\Shipping;

use App\Models\ShippingMethod;
use App\Services\BaseService;
use Illuminate\Support\Str;

class ShippingMethodService extends BaseService
{
    public function __construct(
        private ShippingRestrictionService $shippingRestrictionService,
    ) {
        parent::__construct();
    }

    public function createShippingMethod(array $data)
    {
        $rates = $data['rates'] ?? [];
        unset($data['rates']);
        $restrictions = $data['restrictions'] ?? null;
        unset($data['restrictions']);

        $data['name'] = Str::slug($data['label']);
        $shippingMethod = new ShippingMethod($data);
        if (!$shippingMethod->save()) {
            throw new \Exception('Error creating shipping method');
        }
        $this->saveShippingRates($shippingMethod, $rates);
        if (is_array($restrictions)) {
            $this->syncShippingRestrictions($shippingMethod, $restrictions);
        }
        return true;
    }
    public function updateShippingMethod(ShippingMethod $shippingMethod, array $data)
    {
        $rates = $data['rates'] ?? [];
        unset($data['rates']);
        $restrictions = $data['restrictions'] ?? null;
        unset($data['restrictions']);
        if (!empty($data['label'])) {
            $data['name'] = Str::slug($data['label']);
        }
        if (!$shippingMethod->update($data)) {
            throw new \Exception('Error updating shipping method');
        }
        $this->saveShippingRates($shippingMethod, $rates);
        if (is_array($restrictions)) {
            $this->syncShippingRestrictions($shippingMethod, $restrictions);
        }
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

    public function syncShippingRestrictions(ShippingMethod $shippingMethod, array $restrictions)
    {
        $shippingMethod->restrictions()
            ->whereNotIn('id', array_column($restrictions, 'restriction_id'))
            ->delete();
        foreach ($restrictions as $restriction) {
            if (empty($restriction['action'])) {
                continue; // Skip if action is not set
            }
            if (!empty($restriction['id'])) {
                $shippingRestriction = $shippingMethod->restrictions()->find($restriction['id']);
                if (!$shippingRestriction) {
                    throw new \Exception("Shipping restriction not found for ID: {$restriction['id']}");
                }
                $this->shippingRestrictionService->updateShippingRestriction(
                    $shippingRestriction,
                    $restriction
                );
            } else {
                $this->shippingRestrictionService->createShippingRestriction(
                    $shippingMethod,
                    $restriction
                );
            }
        }
        return true;
    }

    public function destroyBulkShippingMethods(array $ids)
    {
        $shippingMethods = ShippingMethod::whereIn('id', $ids)->get();
        foreach ($shippingMethods as $shippingMethod) {
            if (!$shippingMethod->delete()) {
                throw new \Exception("Error deleting shipping method with ID: {$shippingMethod->id}");
            }
        }
        return true;
    }
}
