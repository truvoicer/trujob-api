<?php

namespace App\Services\Listing;

use App\Contracts\Shipping\ShippingRestriction;
use App\Models\Listing;
use App\Models\ShippingRestriction as ModelsShippingRestriction;
use App\Repositories\ListingRepository;

class ListingsShippingRestrictionService implements ShippingRestriction
{
    public function __construct(
        protected ListingRepository $listingRepository,
    ) {}
    public function validateRequest(): bool
    {
        request()->validate(['restriction_id' => 'exists:listings,id']);
        return true;
    }
    public function storeShippingRestriction(array $data): ModelsShippingRestriction
    {
        $data['restrictable_type'] = Listing::class;
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
