<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Requests\Product\StoreMediaProductRequest;
use App\Http\Resources\Product\MediaProductResource;
use App\Http\Resources\Product\ProductListResource;
use App\Models\Product;
use App\Models\MediaProduct;
use App\Services\Product\ProductsMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends ProductController
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
    public function createMediaProduct(Product $product, Request $request)
    {
        $this->productsAdminService->setUser($request->user());
        $this->productsAdminService->setProduct($product);
        $createMediaProduct = $this->productsAdminService->createMediaProduct($request->all());
        if (!$createMediaProduct) {
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
     * @param StoreMediaProductRequest $request
     * @return JsonResponse
     */
    public function updateMediaProduct(MediaProduct $productMedia, Request $request, ProductsMediaService $productsMediaService)
    {
        $productsMediaService->setUser($request->user());
        $productsMediaService->setMediaProduct($productMedia);
        $createMediaProduct = $productsMediaService->updateMediaProduct($request->all());
        if (!$createMediaProduct) {
            return $this->sendErrorResponse(
                'Error updating product media',
                [],
                $this->productsAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse(
            'Product media updated',
            new MediaProductResource($productsMediaService->getMediaProduct()),
            $this->productsAdminService->getErrors()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMediaProductRequest $request
     * @return JsonResponse
     */
    public function deleteMediaProduct(MediaProduct $productMedia, ProductsMediaService $productsMediaService)
    {
        $productsMediaService->setMediaProduct($productMedia);
        $deleteMediaProduct = $productsMediaService->deleteMediaProduct();
        if (!$deleteMediaProduct) {
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
