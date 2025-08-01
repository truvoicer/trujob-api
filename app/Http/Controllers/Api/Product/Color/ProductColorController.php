<?php

namespace App\Http\Controllers\Api\Product\Color;

use App\Http\Controllers\Controller;
use App\Http\Resources\Color\ColorResource;
use App\Models\Color;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductColorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductColorController extends Controller
{
    public function __construct(
        private ProductColorService $productColorService,
        private ProductRepository $productRepository,
    )
    {
    }

    public function index(Product $product, Request $request) {
        $this->productRepository->setQuery(
            $product->colors()
        );
        $this->productRepository->setPagination(true);
        $this->productRepository->setOrderByColumn(
            $request->get('sort', 'label')
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

        return ColorResource::collection(
            $this->productRepository->findMany()
        );
    }

    public function store(Product $product, Color $color, Request $request)
    {
        $this->productColorService->setUser($request->user()->user);
        $this->productColorService->setSite($request->user()->site);

        if (
            !$this->productColorService->attachColorToProduct(
                $product,
                $color,
            )
        ) {
            return response()->json([
                'message' => 'Error adding product color',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Added product color',
        ], Response::HTTP_CREATED);
    }
    public function destroy(Product $product, Color $color, Request $request)
    {
        $this->productColorService->setUser($request->user()->user);
        $this->productColorService->setSite($request->user()->site);

        if (
            !$this->productColorService->detachColorFromProduct(
                $product,
                $color,
            )
        ) {
            return response()->json([
                'message' => 'Error removing product color',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Removed product color',
        ], Response::HTTP_OK);
    }
}
