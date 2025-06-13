<?php

namespace App\Http\Controllers\Api\Product\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\Media\StoreProductMediaRequest;
use App\Http\Requests\Product\Media\UpdateProductMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Models\Product;
use App\Services\Product\ProductMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class ProductMediaController extends Controller
{
    public function __construct(
        private ProductMediaService $productsMediaService,
    ) {
    }

    public function index(Product $product, Request $request): AnonymousResourceCollection
    {
        $this->productsMediaService->setUser($request->user()->user);
        $this->productsMediaService->setSite($request->user()->site);

        $medias = $product->media()->get();
        return MediaResource::collection($medias);
    }

    public function store(Product $product, StoreProductMediaRequest $request): JsonResponse
    {
        $this->productsMediaService->setUser($request->user()->user);
        $this->productsMediaService->setSite($request->user()->site);

        $media = $this->productsMediaService->attachMediaToProduct(
            $product,
            $request->validated('media')
        );

        if (!$media) {
            return response()->json([
                'message' => 'Error creating product media',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'message' => 'Product media created',
        ], Response::HTTP_CREATED);
    }
    public function destroy(Product $product, Media $media, Request $request): JsonResponse
    {
        $this->productsMediaService->setUser($request->user()->user);
        $this->productsMediaService->setSite($request->user()->site);

        if (!$this->productsMediaService->detachMediaFromProduct($product, $media)) {
            return response()->json([
                'message' => 'Error removing product media',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'message' => 'Product media removed',
        ], Response::HTTP_OK);
    }

    public function show(Product $product, Media $media, Request $request): JsonResource
    {
        $this->productsMediaService->setUser($request->user()->user);
        $this->productsMediaService->setSite($request->user()->site);

        return new MediaResource($media);
    }

    public function update(Product $product, Media $media, UpdateProductMediaRequest $request): JsonResponse
    {
        $this->productsMediaService->setUser($request->user()->user);
        $this->productsMediaService->setSite($request->user()->site);

        $media = $this->productsMediaService->updateMedia(
            $media,
            $request->validated('media')
        );

        if (!$media) {
            return response()->json([
                'message' => 'Error updating product media',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(new MediaResource($media), Response::HTTP_OK);
    }

}
