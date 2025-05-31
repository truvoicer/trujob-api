<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Resources\Product\ProductSingleResource;
use App\Models\Product;

class ProductPrivateController extends ProductBaseController
{

    public function show(Product $product)
    {
        return new ProductSingleResource($product);
    }

}
