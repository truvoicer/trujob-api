<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Resources\Product\ProductAdminListResource;
use App\Models\Product;
use Illuminate\Http\Request;

class UserProductController extends ProductBaseController
{


    public function index(Request $request)
    {
        $this->productsFetchService->setUser($request->user()->user);
        $this->productsFetchService->setSite($request->user()->site);

        $this->productRepository->setQuery(
            $request->user()->user->products()
        );
        $this->productRepository->setPagination(true);
        $this->productRepository->setOrderByColumn(
            $request->get('sort', 'id')
        );
        $this->productRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->productRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->productRepository->setPage(
            $request->get('page', 1)
        );
        $search = $request->get('query', null);
        if ($search) {
            $this->productRepository->addWhere(
                'title',
                "%$search%",
                'like',
            );
        }
        return ProductAdminListResource::collection(
            $this->productRepository->findMany()
        );
    }

    public function show(Product $product)
    {
        return new ProductAdminListResource($product);
    }
}
