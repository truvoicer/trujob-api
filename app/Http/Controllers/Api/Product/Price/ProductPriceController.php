<?php

namespace App\Http\Controllers\Api\Product\Price;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\Price\CreateProductPriceRequest;
use App\Http\Requests\Product\Price\EditProductPriceRequest;
use App\Http\Resources\Price\PriceResource;
use App\Models\Price;
use App\Models\Product;
use App\Repositories\ProductPriceRepository;
use App\Services\Product\ProductPriceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductPriceController extends Controller
{

    public function __construct(
        private ProductPriceService $productPriceService,
        private ProductPriceRepository $productPriceRepository,
    ) {}

    public function index(Product $product, Request $request) {
        $this->productPriceRepository->setQuery(
            $product->prices()
        );
        $this->productPriceRepository->setPagination(true);
        $this->productPriceRepository->setOrderByColumn(
            $request->get('sort', 'created_at')
        );
        $this->productPriceRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->productPriceRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->productPriceRepository->setPage(
            $request->get('page', 1)
        );

        return PriceResource::collection(
            $this->productPriceRepository->findMany()
        );
    }

    public function show(Product $product, Price $price, Request $request) {
        $this->productPriceService->setUser($request->user()->user);
        $this->productPriceService->setSite($request->user()->site);
        return new PriceResource($price);
    }

    public function store(Product $product, CreateProductPriceRequest $request)
    {
        $this->productPriceService->setUser($request->user()->user);
        $this->productPriceService->setSite($request->user()->site);

        if (
            !$this->productPriceService->createproductPrice(
                $product,
                $request->validated(),
            )
        ) {
            return response()->json([
                'message' => 'Error adding product price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Added product price',
        ], Response::HTTP_CREATED);
    }

    public function update(Product $product, Price $price, EditProductPriceRequest $request)
    {
        $this->productPriceService->setUser($request->user()->user);
        $this->productPriceService->setSite($request->user()->site);

        if (
            !$this->productPriceService->updateproductPrice(
                $product,
                $price,
                $request->validated()
            )
        ) {
            return response()->json([
                'message' => 'Error updating product price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Updated product price',
        ], Response::HTTP_OK);
    }

    public function destroy(Product $product, Price $price, Request $request)
    {
        $this->productPriceService->setUser($request->user()->user);
        $this->productPriceService->setSite($request->user()->site);

        if (
            !$this->productPriceService->deleteproductPrice(
                $product,
                $price
            )
        ) {
            return response()->json([
                'message' => 'Error deleting product price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Deleted product price',
        ], Response::HTTP_OK);
    }
}
