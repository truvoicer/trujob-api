<?php

namespace App\Services\Locale;

use App\Contracts\Shipping\ShippingZoneAbleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Product\CountryResource;
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
    // public function syncShippingZoneAble(ShippingZone $shippingZone, array $data): void
    // {
    //     $shippingZone->shippingZoneAbles()->where('shipping_zoneable_type', MorphEntity::COUNTRY->value)
    //         ->whereNotIn('shipping_zoneable_id', array_column($data, 'shipping_zoneable_id'))
    //         ->delete();
    //     $this->attachShippingZoneAble($shippingZone, $data);
    // }

    public function attachShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $country = $this->countryRepository->findById($data['shipping_zoneable_id']);
        if (!$country) {
            throw new \Exception('Country not found');
        }
        $country->shippingZoneables()->create([
            'shipping_zone_id' => $shippingZone->id,
        ]);
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
                $resource->shipping_zoneable
            )
        ];
    }
}
