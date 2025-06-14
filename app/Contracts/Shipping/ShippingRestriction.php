<?php

namespace App\Contracts\Shipping;

use App\Models\ShippingMethod;
use App\Models\ShippingRestriction as ShippingRestrictionModel;
use Illuminate\Http\Resources\Json\JsonResource;

interface ShippingRestriction
{
    public function validateRequest(): bool;
    public function storeShippingRestriction(ShippingMethod $shippingMethod, array $data): ShippingRestrictionModel;
    public function updateShippingRestriction(ShippingRestrictionModel $shippingRestriction, array $data): ShippingRestrictionModel;
    public function deleteShippingRestriction(ShippingRestrictionModel $shippingRestriction): bool;
    public function getRestrictionableEntityResourceData(JsonResource $resource): array;

}
