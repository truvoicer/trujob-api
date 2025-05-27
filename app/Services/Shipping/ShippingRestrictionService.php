<?php

namespace App\Services\Shipping;

use App\Models\ShippingRestriction;
use App\Services\BaseService;

class ShippingRestrictionService extends BaseService
{
    public function createShippingRestriction(array $data) {

        
        $shippingRestriction = new ShippingRestriction($data);
        if (!$shippingRestriction->save()) {
            throw new \Exception('Error creating shipping restriction');
        }
        return true;
    }
    public function updateShippingRestriction(ShippingRestriction $shippingRestriction, array $data) {
        if (!$shippingRestriction->update($data)) {
            throw new \Exception('Error updating shipping restriction');
        }
        return true;
    }

    public function deleteShippingRestriction(ShippingRestriction $shippingRestriction) {
        if (!$shippingRestriction->delete()) {
            throw new \Exception('Error deleting shipping restriction');
        }
        return true;
    }

}
