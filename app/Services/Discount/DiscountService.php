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
        $this->relatedData($discount, $data);
        return true;
    }
    public function updateDiscount(Discount $discount, array $data) {
        if (!$discount->update($data)) {
            throw new \Exception('Error updating discount');
        }
        $this->relatedData($discount, $data);
        return true;
    }

    public function relatedData(Discount $discount, array $data) {

        if (!empty($data['products']) && is_array($data['products'])) {
            
        }

        if (!empty($data['category_ids']) && is_array($data['category_ids'])) {
            $discount->categories()->sync($data['category_ids']);
        }
        return $discount;
    }

    public function deleteDiscount(Discount $discount) {
        if (!$discount->delete()) {
            throw new \Exception('Error deleting discount');
        }
        return true;
    }

}
