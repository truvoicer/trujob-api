<?php

namespace App\Http\Controllers\Api\Product\Media;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductMediaService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductMediaController extends Controller
{

    public function __construct(
        private ProductMediaService $productMediaService,
    )
    {
    }

    public function store(Product $product, Request $request) {
        $this->productMediaService->setUser($request->user()->user);
        $this->productMediaService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('media')->validate();
        if (
            !$this->productMediaService->attachBulkMediasToProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error creating product media',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product media created',
        ], Response::HTTP_CREATED);
    }

    public function destroy(Product $product, Request $request) {
        $this->productMediaService->setUser($request->user()->user);
        $this->productMediaService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('media')->validate();
        if (
            !$this->productMediaService->detachBulkMediasFromProduct(
                $product,
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing product media',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Product media removed',
        ], Response::HTTP_OK);
    }
}
