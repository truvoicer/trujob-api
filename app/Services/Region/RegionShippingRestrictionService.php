<?php

namespace App\Services\Region;

use App\Contracts\Shipping\ShippingRestriction;
use App\Enums\MorphEntity;
use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Http\Resources\Product\RegionResource;
use App\Http\Resources\Region\RegionResource as RegionRegionResource;
use App\Models\ShippingMethod;
use App\Repositories\RegionRepository;
use App\Models\ShippingRestriction as ModelsShippingRestriction;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionShippingRestrictionService implements ShippingRestriction
{
    public function __construct(
        protected RegionRepository $regionRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['restriction_id' => 'exists:regions,id']);
        return true;
    }
    public function storeShippingRestriction(ShippingMethod $shippingMethod, array $data): ModelsShippingRestriction
    {
        $region = $this->regionRepository->findById($data['restriction_id']);
        if (!$region) {
            throw new \Exception('Region not found');
        }
        return $region->shippingRestrictions()->create([
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
            'region' => new RegionRegionResource(
                $resource->restrictionable
            )
        ];
    }
}
