<?php

namespace App\Services\Shipping;

use App\Enums\Order\Shipping\ShippingRestrictionType;
use App\Factories\Shipping\ShippingRestrictionFactory;
use App\Helpers\EnumHelpers;
use App\Models\ShippingRestriction;
use App\Services\BaseService;

class ShippingRestrictionService extends BaseService
{
    public function createShippingRestriction(array $data): ShippingRestriction
    {
        return ShippingRestrictionFactory::create(
            EnumHelpers::validateMorphEnumByArray(ShippingRestrictionType::class, 'type', $data)
        )->storeShippingRestriction($data);
    }
    public function updateShippingRestriction(ShippingRestriction $shippingRestriction, array $data): ShippingRestriction
    {
        return ShippingRestrictionFactory::create(
            EnumHelpers::validateMorphEnumByType(
                ShippingRestrictionType::class,
                'type',
                $shippingRestriction->restrictable_type
            )
        )->updateShippingRestriction($shippingRestriction, $data);
    }

    public function deleteShippingRestriction(ShippingRestriction $shippingRestriction): bool
    {
        return ShippingRestrictionFactory::create(
            EnumHelpers::validateMorphEnumByType(
                ShippingRestrictionType::class,
                'type',
                $shippingRestriction->restrictable_type
            )
        )->deleteShippingRestriction($shippingRestriction);
    }
}
