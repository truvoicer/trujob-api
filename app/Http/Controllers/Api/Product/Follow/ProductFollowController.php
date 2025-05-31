<?php

namespace App\Http\Controllers\Api\Product\Follow;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductFollowRequest;
use App\Http\Requests\Product\UpdateProductFollowRequest;
use App\Http\Resources\Product\ProductFollowResource;
use App\Models\Product;
use App\Models\ProductFollow;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductFollowService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductFollowController extends Controller
{

    public function __construct(
        private ProductFollowService $productFollowService,
        private ProductRepository $productRepository,
    ) {}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product, Request $request)
    {
        $this->productRepository->setQuery(
            $product->follows()
        );
        $this->productRepository->setPagination(true);
        $this->productRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->productRepository->setOrderDir(
            $request->get('order', 'desc')
        );
        $this->productRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->productRepository->setPage(
            $request->get('page', 1)
        );

        return ProductFollowResource::collection(
            $this->productRepository->findMany()
        );
    }
    public function store(Product $product, StoreProductFollowRequest $request)
    {
        $this->productFollowService->setUser($request->user()->user);
        $this->productFollowService->setSite($request->user()->site);

        if (
            !$this->productFollowService->createProductFollow(
                $product,
                $request->validated('user_ids', [])
            )
        ) {
            return response()->json([
                'message' => 'Error creating product follow',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product follow created',
        ], Response::HTTP_CREATED);
    }

    public function update(Product $product, ProductFollow $productFollow, UpdateProductFollowRequest $request)
    {
        $this->productFollowService->setUser($request->user()->user);
        $this->productFollowService->setSite($request->user()->site);

        if (!$this->productFollowService->updateProductFollow($productFollow, $request->validated())) {
            return response()->json([
                'message' => 'Error updating product follow',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product follow updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Product $product, ProductFollow $productFollow, Request $request)
    {
        $this->productFollowService->setUser($request->user()->user);
        $this->productFollowService->setSite($request->user()->site);

        if (!$this->productFollowService->deleteProductFollow($productFollow)) {
            return response()->json([
                'message' => 'Error deleting product follow',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product follow deleted',
        ], Response::HTTP_OK);
    }
}
