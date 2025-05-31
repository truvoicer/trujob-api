<?php

namespace App\Http\Controllers\Api\Product\Media;

use App\Http\Requests\Product\StoreProductMediaRequest;
use App\Http\Resources\Product\ProductMediaResource;
use App\Http\Resources\Product\ProductListResource;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Services\Product\ProductsMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductMediaController extends ProductController
{
    /**
     * Display a product of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fetchMedia()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function createProductMedia(Product $product, Request $request)
    {
        $this->productsAdminService->setUser($request->user());
        $this->productsAdminService->setProduct($product);
        $createProductMedia = $this->productsAdminService->createProductMedia($request->all());
        if (!$createProductMedia) {
            return $this->sendErrorResponse(
                'Error creating product media',
                [],
                $this->productsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse(
            'Product media created',
            new ProductListResource($this->productsAdminService->getProduct()),
            $this->productsAdminService->getErrors()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductMediaRequest $request
     * @return JsonResponse
     */
    public function updateProductMedia(ProductMedia $productMedia, Request $request, ProductsMediaService $productsMediaService)
    {
        $productsMediaService->setUser($request->user());
        $productsMediaService->setProductMedia($productMedia);
        $createProductMedia = $productsMediaService->updateProductMedia($request->all());
        if (!$createProductMedia) {
            return $this->sendErrorResponse(
                'Error updating product media',
                [],
                $this->productsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse(
            'Product media updated',
            new ProductMediaResource($productsMediaService->getProductMedia()),
            $this->productsAdminService->getErrors()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductMediaRequest $request
     * @return JsonResponse
     */
    public function deleteProductMedia(ProductMedia $productMedia, ProductsMediaService $productsMediaService)
    {
        $productsMediaService->setProductMedia($productMedia);
        $deleteProductMedia = $productsMediaService->deleteProductMedia();
        if (!$deleteProductMedia) {
            return $this->sendErrorResponse(
                'Error deleting product media',
            [],
                $this->productsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse(
            'Product media deleted',
            [],
            $this->productsAdminService->getErrors()
        );
    }


}
