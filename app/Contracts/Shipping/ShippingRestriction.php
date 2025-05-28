<?php

namespace App\Contracts\Shipping;

use App\Models\ShippingRestriction as ShippingRestrictionModel;

interface ShippingRestriction
{
    public function validateRequest(): bool;
    public function storeShippingRestriction(array $data): ShippingRestrictionModel;
    public function updateShippingRestriction(ShippingRestrictionModel $shippingRestriction, array $data): ShippingRestrictionModel;
    public function deleteShippingRestriction(ShippingRestrictionModel $shippingRestriction): bool;

}
