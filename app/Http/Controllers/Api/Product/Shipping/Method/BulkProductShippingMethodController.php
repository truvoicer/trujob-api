<?php

namespace App\Http\Controllers\Api\Product\Shipping\Method;

use App\Enums\Product\Shipping\Method\ProductableShippingMethodType;
use App\Factories\Product\Shipping\Method\ProductableShippingMethodFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\Shipping\Method\BulkProductShippingMethodRequest;
use App\Models\Product;
use App\Services\Shipping\ShippingMethodService;
use Symfony\Component\HttpFoundation\Response;

class BulkProductShippingMethodController extends Controller
{

    public function __construct(
        private ShippingMethodService $shippingMethodService,
    ) {}

    public function store(Product $product, BulkProductShippingMethodRequest $request)
    {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);

        $sync = ProductableShippingMethodFactory::create(
            ProductableShippingMethodType::PRODUCT
        )->attachBulkShippingMethodsToProductable(
            $product,
            $request->validated('ids', [])
        );

        if (!$sync) {
            return response()->json([
                'message' => 'Error attaching shipping methods to product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping methods attached to product',
        ], Response::HTTP_OK);
    }

    public function destroy(Product $product, BulkProductShippingMethodRequest $request)
    {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);

        $sync = ProductableShippingMethodFactory::create(
            ProductableShippingMethodType::PRODUCT
        )->detachBulkShippingMethodsFromProductable(
            $product,
            $request->validated('ids', [])
        );

        if (!$sync) {
            return response()->json([
                'message' => 'Error removing shipping methods',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping methods removed',
        ], Response::HTTP_OK);
    }
}
