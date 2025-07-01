<?php

namespace App\Http\Controllers\Api\Product\Shipping\Method;

use App\Enums\Product\Shipping\Method\ProductableShippingMethodType;
use App\Factories\Product\Shipping\Method\ProductableShippingMethodFactory;
use App\Http\Controllers\Controller;
use App\Http\Resources\Shipping\ShippingMethodResource;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Repositories\ShippingMethodRepository;
use App\Services\Shipping\ShippingMethodService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductShippingMethodController extends Controller
{

    public function __construct(
        private ShippingMethodService $shippingMethodService,
        private ShippingMethodRepository $shippingMethodRepository,
    )
    {
    }

    public function index(Product $product, Request $request) {
        $this->shippingMethodRepository->setQuery(
            $product->shippingMethods()
        );
        $this->shippingMethodRepository->setPagination(true);
        $this->shippingMethodRepository->setOrderByColumn(
            $request->get('sort', 'name')
        );
        $this->shippingMethodRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->shippingMethodRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->shippingMethodRepository->setPage(
            $request->get('page', 1)
        );


        return ShippingMethodResource::collection(
            $this->shippingMethodRepository->findMany()
        );
    }

    public function show(Product $product, ShippingMethod $shippingMethod, Request $request) {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);

        return new ShippingMethodResource(
            $shippingMethod
        );
    }

    public function store(Product $product, ShippingMethod $shippingMethod, Request $request) {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);
        $store = ProductableShippingMethodFactory::create(
            ProductableShippingMethodType::PRODUCT
        )->attachShippingMethodToProductable($product, $shippingMethod);
        if (!$store) {
            return response()->json([
                'message' => 'Error creating shipping method',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping method created',
        ], Response::HTTP_OK);
    }


    public function destroy(Product $product, ShippingMethod $shippingMethod, Request $request) {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);

        $destroy = ProductableShippingMethodFactory::create(
            ProductableShippingMethodType::PRODUCT
        )->detachShippingMethodFromProductable($product, $shippingMethod);
        if (!$destroy) {
            return response()->json([
                'message' => 'Error deleting shipping method',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping method deleted',
        ], Response::HTTP_OK);
    }
}
