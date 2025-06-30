<?php

namespace App\Services\Region;

use App\Contracts\Shipping\ShippingZoneAbleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Region\RegionResource;
use App\Models\Region;
use App\Models\ShippingZone;
use App\Repositories\RegionRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionShippingZoneAbleService implements ShippingZoneAbleInterface
{
    public function __construct(
        private RegionRepository $regionRepository,
    ) {
    }

    public function validateRequest(): bool
    {
        request()->validate(['shipping_zoneable_id' => 'exists:regions,id']);
        return true;
    }

    public function attachShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $region = $this->regionRepository->findById($data['shipping_zoneable_id']);

        if (!$region) {
            throw new \Exception('Region not found');
        }
        $region->shippingZoneAbles()->create([
            'shipping_zone_id' => $shippingZone->id,
        ]);
    }

    public function syncShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $shippingZone->shippingZoneAbles()->where('shipping_zoneable_type', MorphEntity::REGION->value)
            ->whereNotIn('shipping_zoneable_id', array_column($data, 'shipping_zoneable_id'))
            ->delete();
        $doesntExist = array_filter($data, function ($item) use ($shippingZone) {
            return !$shippingZone->shippingZoneAbles()
                ->where('shipping_zoneable_type', MorphEntity::REGION->value)
                ->where('shipping_zoneable_id', $item['shipping_zoneable_id'])
                ->exists();
        });
        foreach ($doesntExist as $doesntExistItem) {
            $this->attachShippingZoneAble($shippingZone, $doesntExistItem);
        }
    }

    public function detachShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $shippingZone->shippingZoneAbles()->where('shipping_zoneable_type', MorphEntity::REGION->value)
            ->where('shipping_zoneable_id', $data['shipping_zoneable_id'])
            ->delete();
    }

    public function getShippingZoneableEntityResourceData(JsonResource $resource): array
    {
        return [
            'region' => new RegionResource(
                $resource->shippingZoneAble
            )
        ];
    }
}
