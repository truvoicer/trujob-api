<?php

namespace App\Services\Discount;

use App\Models\Discount;
use App\Services\BaseService;

class DiscountService extends BaseService
{

    public function createDiscount(array $data) {
        $discount = new Discount($data);
        if (!$discount->save()) {
            throw new \Exception('Error creating discount');
        }
        return true;
    }
    public function updateDiscount(Discount $discount, array $data) {
        if (!$discount->update($data)) {
            throw new \Exception('Error updating discount');
        }
        return true;
    }

    public function deleteDiscount(Discount $discount) {
        if (!$discount->delete()) {
            throw new \Exception('Error deleting discount');
        }
        return true;
    }

}
