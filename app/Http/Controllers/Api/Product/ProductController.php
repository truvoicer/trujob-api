<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductListResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends ProductBaseController
{

    public function index(Request $request)
    {
        $this->productsFetchService->setUser($request->user()->user);
        $this->productsFetchService->setSite($request->user()->site);

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
        return ProductListResource::collection(
            $this->productRepository->findMany()
        );
    }

    public function show(Product $product)
    {
        return new ProductListResource($product);
    }

    public function store(CreateProductRequest $request)
    {
        $this->productsAdminService->setUser($request->user()->user);
        $this->productsAdminService->setSite($request->user()->site);
        if (!$this->productsAdminService->createProduct($request->validated())) {
            return response()->json([
                'message' => 'Error creating product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product created',
        ], Response::HTTP_CREATED);
    }

    public function update(Product $product, UpdateProductRequest $request)
    {
        $this->productsAdminService->setUser($request->user()->user);
        $this->productsAdminService->setSite($request->user()->site);

        if (!$this->productsAdminService->updateProduct($product, $request->validated())) {
            return response()->json([
                'message' => 'Error updating product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Product $product, Request $request)
    {
        $this->productsAdminService->setUser($request->user()->user);
        $this->productsAdminService->setSite($request->user()->site);

        if (!$this->productsAdminService->deleteProduct($product)) {
            return response()->json([
                'message' => 'Error deleting product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product deleted',
        ], Response::HTTP_OK);
    }
}
