<?php

namespace App\Services\Product\Shipping\Method;

use App\Contracts\Product\Shipping\Method\ProductableShippingMethodInterface;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Repositories\ShippingMethodRepository;
use Illuminate\Database\Eloquent\Model;

class ProductShippingMethodService implements ProductableShippingMethodInterface
{
    public function __construct(
        protected ShippingMethodRepository $shippingMethodRepository
    ) {}

    public function attachBulkShippingMethodsToProductable(
        Model $productable,
        array $shippingMethodIds = []
    ): bool {
        if (!$productable instanceof Product) {
            throw new \Exception('Productable must be an instance of Product');
        }

        $doesntExist = array_filter($shippingMethodIds, function ($item) use ($productable) {
            return !$productable->productableShippingMethods()
                ->where('shipping_method_id', $item)
                ->exists();
        });
        $productable->productableShippingMethods()->createMany(
            array_map(function ($shippingMethodId) use ($productable) {
                return [
                    'shipping_method_id' => $shippingMethodId,
                ];
            }, $doesntExist)
        );
        return true;
    }

    public function detachBulkShippingMethodsFromProductable(
        Model $productable,
        array $shippingMethodIds = []
    ): bool {
        if (!$productable instanceof Product) {
            throw new \Exception('Productable must be an instance of Product');
        }
        $productable->productableShippingMethods()->whereIn('shipping_method_id', $shippingMethodIds)->delete();
        return true;
    }

    public function syncBulkShippingMethodWithProductable(
        Model $productable,
        array $shippingMethodIds = []
    ): bool {
        if (!$productable instanceof Product) {
            throw new \Exception('Productable must be an instance of Product');
        }

        $productable->productableShippingMethods()
            ->whereNotIn('shipping_method_id', $shippingMethodIds)
            ->delete();
        $doesntExist = array_filter($shippingMethodIds, function ($item) use ($productable) {
            return !$productable->productableShippingMethods()
                ->where('shipping_method_id', $item)
                ->exists();
        });

        $this->attachBulkShippingMethodsToProductable($productable, $doesntExist);
        return true; // Placeholder return value
    }

    public function attachShippingMethodToProductable(
        Model $productable,
        ShippingMethod $shippingMethod
    ): bool {
        if (!$productable instanceof Product) {
            throw new \Exception('Productable must be an instance of Product');
        }
        if ($productable->productableShippingMethods()
            ->where('shipping_method_id', $shippingMethod->id)
            ->exists()) {
            return true; // Shipping method already attached
        }
        $productable->productableShippingMethods()->create([
            'shipping_method_id' => $shippingMethod->id,
        ]);
        return true; // Placeholder return value
    }

    public function detachShippingMethodFromProductable(
        Model $productable,
        ShippingMethod $shippingMethod,
    ): bool {

        if (!$productable instanceof Product) {
            throw new \Exception('Productable must be an instance of Product');
        }
        $productable->productableShippingMethods()->where('shipping_method_id', $shippingMethod->id)->delete();
        return true; // Placeholder return value
    }
}
