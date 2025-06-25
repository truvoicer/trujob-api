<?php

namespace App\Services\Locale;

use App\Contracts\Shipping\ShippingZoneAbleInterface;
use App\Enums\MorphEntity;
use App\Http\Resources\Currency\CurrencyResource;
use App\Models\ShippingZone;
use App\Repositories\CurrencyRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyShippingZoneAbleService implements ShippingZoneAbleInterface
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['shipping_zoneable_id' => 'exists:currencies,id']);
        return true;
    }
    // public function syncShippingZoneAble(ShippingZone $shippingZone, array $data): void
    // {
    //     $shippingZone->shippingZoneAbles()->where('shipping_zoneable_type', MorphEntity::CURRENCY->value)
    //         ->whereNotIn('shipping_zoneable_id', array_column($data, 'shipping_zoneable_id'))
    //         ->delete();
    //     $this->attachShippingZoneAble($shippingZone, $data);
    // }

    public function attachShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $currency = $this->currencyRepository->findById($data['shipping_zoneable_id']);
        if (!$currency) {
            throw new \Exception('Currency not found');
        }
        $currency->shippingZoneAbles()->create([
            'shipping_zone_id' => $shippingZone->id,
        ]);
    }

    public function detachShippingZoneAble(ShippingZone $shippingZone, array $data): void
    {
        $shippingZone->shippingZoneAbles()->where('shipping_zoneable_type', MorphEntity::CURRENCY->value)
            ->where('shipping_zoneable_id', $data['shipping_zoneable_id'])
            ->delete();
    }

    public function getShippingZoneableEntityResourceData(JsonResource $resource): array
    {
        return [
            'currency' => new CurrencyResource(
                $resource->shipping_zoneable
            )
        ];
    }
}
