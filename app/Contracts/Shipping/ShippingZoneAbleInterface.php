<?php

namespace App\Contracts\Shipping;

use App\Models\ShippingZone;
use Illuminate\Http\Resources\Json\JsonResource;

interface ShippingZoneAbleInterface
{
    public function validateRequest(): bool;
    public function attachShippingZoneAble(ShippingZone $shippingZone, array $data): void;
    public function detachShippingZoneAble(ShippingZone $shippingZone, array $data): void;
    public function getShippingZoneableEntityResourceData(JsonResource $resource): array;

}
