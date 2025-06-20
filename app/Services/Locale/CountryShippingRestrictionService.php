<?php

namespace App\Services\Locale;

use App\Contracts\Shipping\ShippingRestriction;
use App\Enums\MorphEntity;
use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Http\Resources\Product\CountryResource;
use App\Models\Country;
use App\Models\ShippingMethod;
use App\Models\ShippingRestriction as ModelsShippingRestriction;
use App\Repositories\CountryRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryShippingRestrictionService implements ShippingRestriction
{
    public function __construct(
        private CountryRepository $countryRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['restriction_id' => 'exists:countries,id']);
        return true;
    }
    public function storeShippingRestriction(ShippingMethod $shippingMethod, array $data): ModelsShippingRestriction
    {

        $country = $this->countryRepository->findById($data['restriction_id']);
        if (!$country) {
            throw new \Exception('Country not found');
        }
        return $country->shippingRestrictions()->create([
            'shipping_method_id' => $shippingMethod->id,
        ]);
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
            'country' => new CountryResource(
                $resource->restrictionable
            )
        ];
    }
}
