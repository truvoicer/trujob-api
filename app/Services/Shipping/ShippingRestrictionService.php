<?php

namespace App\Services\Shipping;

use App\Enums\Order\Shipping\ShippingRestrictionType;
use App\Factories\Shipping\ShippingRestrictionFactory;
use App\Models\ShippingRestriction;
use App\Services\BaseService;

class ShippingRestrictionService extends BaseService
{
    public function createShippingRestriction(array $data): ShippingRestriction
    {
        return ShippingRestrictionFactory::create(
            ShippingRestrictionType::from($data['type'])
        )->storeShippingRestriction($data);
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
}
