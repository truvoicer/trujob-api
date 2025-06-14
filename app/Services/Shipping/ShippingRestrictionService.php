<?php

namespace App\Services\Shipping;

use App\Enums\Order\Shipping\ShippingRestrictionType;
use App\Factories\Shipping\ShippingRestrictionFactory;
use App\Models\ShippingMethod;
use App\Models\ShippingRestriction;
use App\Services\BaseService;

class ShippingRestrictionService extends BaseService
{
    public function createShippingRestriction(ShippingMethod $shippingMethod, array $data): ShippingRestriction
    {
        return ShippingRestrictionFactory::create(
            ShippingRestrictionType::from($data['type'])
        )->storeShippingRestriction($shippingMethod, $data);
    }
    public function updateShippingRestriction(ShippingRestriction $shippingRestriction, array $data): ShippingRestriction
    {
        return ShippingRestrictionFactory::create(
            ShippingRestrictionType::fromClassName($shippingRestriction->restrictionable_type)
        )->updateShippingRestriction($shippingRestriction, $data);
    }

    public function deleteShippingRestriction(ShippingRestriction $shippingRestriction): bool
    {
        return ShippingRestrictionFactory::create(
            ShippingRestrictionType::fromClassName($shippingRestriction->restrictionable_type)
        )->deleteShippingRestriction($shippingRestriction);
    }

    public function destroyBulkShippingRestrictions(array $ids): bool
    {
        $shippingRestrictions = ShippingRestriction::whereIn('id', $ids)->get();
        foreach ($shippingRestrictions as $shippingRestriction) {
            if (!$this->deleteShippingRestriction($shippingRestriction)) {
                return false;
            }
        }
        return true;
    }
}
