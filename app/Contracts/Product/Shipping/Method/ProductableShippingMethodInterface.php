<?php

namespace App\Contracts\Product\Shipping\Method;

use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Model;

interface ProductableShippingMethodInterface
{
    public function attachBulkShippingMethodsToProductable(
        Model $productable,
        array $shippingMethodIds = []
    ): bool;
    public function detachBulkShippingMethodsFromProductable(
        Model $productable,
        array $shippingMethodIds = []
    ): bool;
    public function syncBulkShippingMethodWithProductable(
        Model $productable,
        array $shippingMethodIds = []
    ): bool;
    public function attachShippingMethodToProductable(
        Model $productable,
        ShippingMethod $shippingMethod,
    ): bool;
    public function detachShippingMethodFromProductable(
        Model $productable,
        ShippingMethod $shippingMethod
    ): bool;
}
