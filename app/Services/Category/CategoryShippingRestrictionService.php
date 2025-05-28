<?php

namespace App\Services\Category;

use App\Contracts\Shipping\ShippingRestriction;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Models\ShippingRestriction as ModelsShippingRestriction;

class CategoryShippingRestrictionService implements ShippingRestriction
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }
    public function validateRequest(): bool
    {
        request()->validate(['restriction_id' => 'exists:categories,id']);
        return true;
    }
    public function storeShippingRestriction(array $data): ModelsShippingRestriction
    {
        $data['restrictable_type'] = Category::class;
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

