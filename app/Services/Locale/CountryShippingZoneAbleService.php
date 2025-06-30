<?php

namespace App\Services\Locale;

use App\Contracts\Shipping\ShippingZoneAbleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Country\CountryResource;
use App\Models\ShippingZone;
use App\Repositories\CountryRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryShippingZoneAbleService implements ShippingZoneAbleInterface
{
    public function __construct(
        private CountryRepository $countryRepository,
    ) {
    }

    public function validateRequest(): bool
    {
        request()->validate(['shipping_zoneable_id' => 'exists:countries,id']);
        return true;
    }

    public function attachShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $country = $this->countryRepository->findById($data['shipping_zoneable_id']);
        if (!$country) {
            throw new \Exception('Country not found');
        }
        $country->shippingZoneAbles()->create([
            'shipping_zone_id' => $shippingZone->id,
        ]);
    }

    public function syncShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $shippingZone->shippingZoneAbles()->where('shipping_zoneable_type', MorphEntity::COUNTRY->value)
            ->whereNotIn('shipping_zoneable_id', array_column($data, 'shipping_zoneable_id'))
            ->delete();
        $doesntExist = array_filter($data, function ($item) use ($shippingZone) {
            return !$shippingZone->shippingZoneAbles()
                ->where('shipping_zoneable_type', MorphEntity::COUNTRY->value)
                ->where('shipping_zoneable_id', $item['shipping_zoneable_id'])
                ->exists();
        });
        foreach ($doesntExist as $doesntExistItem) {
            $this->attachShippingZoneAble($shippingZone, $doesntExistItem);
        }
    }

    public function detachShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $shippingZone->shippingZoneAbles()->where('shipping_zoneable_type', MorphEntity::COUNTRY->value)
            ->where('shipping_zoneable_id', $data['shipping_zoneable_id'])
            ->delete();
    }

    public function getShippingZoneableEntityResourceData(JsonResource $resource): array
    {
        return [
            'country' => new CountryResource(
                $resource->shippingZoneAble
            )
        ];
    }
}
