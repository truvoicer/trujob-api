<?php

namespace App\Services\Locale;

use App\Contracts\Shipping\ShippingRestriction;
use App\Models\Region;
use App\Repositories\RegionRepository;
use App\Models\ShippingRestriction as ModelsShippingRestriction;

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
    public function storeShippingRestriction(array $data): ModelsShippingRestriction
    {
        $data['restrictable_type'] = Region::class;
        $data['restrictable_id'] = $data['restriction_id'];
        $shippingRestriction = new ModelsShippingRestriction($data);
        if (!$shippingRestriction->save()) {
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
}
