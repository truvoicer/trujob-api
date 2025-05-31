<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Requests\Product\ProductFetchRequest;
use App\Http\Resources\Product\ProductListResource;

class ProductPublicController extends ProductBaseController
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ProductFetchRequest $request)
    {
        $this->productsFetchService->setLimit($request->query->getInt('limit', 10));
        $this->productsFetchService->setPage($request->query->getInt('page', 1));

        return ProductListResource::collection(
            $this->productsFetchService->productsFetch(
                $this->productsFetchService->handleRequest($request)
            )
        )->additional([
            'meta' => [
                'has_more' => $this->productsFetchService->hasMore(),
            ]
        ]);
    }
}
