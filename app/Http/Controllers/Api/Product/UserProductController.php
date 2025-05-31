<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Resources\Product\ProductListResource;
use App\Models\Product;
use Illuminate\Http\Request;

class UserProductController extends ProductBaseController
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->productsFetchService->setUser($request->user());
        $this->productsFetchService->setLimit($request->get('limit'));
//        $productsFetchService->setOffset($request->get('offset'));
        $this->productsFetchService->setPagination(true);
        return ProductListResource::collection(
            $this->productsFetchService->userProductsFetch()
        );
    }

    public function show(Product $product)
    {
        return new ProductListResource($product);
    }
}
