<?php

namespace App\Services\Product;

use App\Contracts\Shipping\ShippingRestriction;
use App\Http\Resources\Product\ProductListResource;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\ShippingRestriction as ModelsShippingRestriction;
use App\Repositories\ProductRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductShippingRestrictionService implements ShippingRestriction
{
    public function __construct(
        protected ProductRepository $productRepository,
    ) {}
    public function validateRequest(): bool
    {
        request()->validate(['restriction_id' => 'exists:products,id']);
        return true;
    }
    public function storeShippingRestriction(ShippingMethod $shippingMethod, array $data): ModelsShippingRestriction
    {
        $data['restrictionable_type'] = Product::class;
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
            'product' => new ProductListResource(
                $resource->restrictionable
            )
        ];
    }
}
