<?php

namespace App\Services\Locale;

use App\Contracts\Shipping\ShippingRestriction;
use App\Enums\MorphEntity;
use App\Http\Resources\Product\CurrencyResource;
use App\Models\Currency;
use App\Models\ShippingMethod;
use App\Repositories\CurrencyRepository;
use App\Models\ShippingRestriction as ModelsShippingRestriction;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyShippingRestrictionService implements ShippingRestriction
{
    public function __construct(
        protected CurrencyRepository $currencyRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['restriction_id' => 'exists:currencies,id']);
        return true;
    }
    public function storeShippingRestriction(ShippingMethod $shippingMethod, array $data): ModelsShippingRestriction
    {
        $data['restrictionable_type'] = MorphEntity::CURRENCY;
        $data['restrictionable_id'] = $data['restriction_id'];
        $shippingRestriction = new ModelsShippingRestriction($data);
        if (!$shippingMethod->restrictions()->save($shippingRestriction)) {
            throw new \Exception('Error creating shipping restriction');
        }
        return $shippingRestriction;
    }
    public function updateShippingRestriction(
        ModelsShippingRestriction $shippingRestriction,
        array $data
    ): ModelsShippingRestriction {
        if (!$shippingRestriction->update($data)) {
            throw new \Exception('Error updating shipping restriction');
        }
        return $shippingRestriction;
    }
    public function deleteShippingRestriction(ModelsShippingRestriction $shippingRestriction): bool
    {
        if (!$shippingRestriction->delete()) {
            throw new \Exception('Error deleting shipping restriction');
        }
        return true;
    }

    public function getRestrictionableEntityResourceData(JsonResource $resource): array
    {
        return [
            'currency' => new CurrencyResource(
                $resource->restrictionable
            )
        ];
    }
}
