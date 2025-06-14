<?php

namespace App\Services\Locale;

use App\Contracts\Shipping\ShippingRestriction;
use App\Http\Resources\Region\RegionResource;
use App\Models\Region;
use App\Models\ShippingMethod;
use App\Repositories\RegionRepository;
use App\Models\ShippingRestriction as ModelsShippingRestriction;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionShippingRestrictionService implements ShippingRestriction
{
    public function __construct(
        private RegionRepository $regionRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['restriction_id' => 'exists:regions,id']);
        return true;
    }
    public function storeShippingRestriction(ShippingMethod $shippingMethod, array $data): ModelsShippingRestriction
    {
        $data['restrictionable_type'] = Region::class;
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
            'region' => new RegionResource(
                $resource->restrictionable
            )
        ];
    }
}
