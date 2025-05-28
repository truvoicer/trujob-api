<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\Method\StoreShippingMethodRequest;
use App\Http\Requests\Shipping\Method\UpdateShippingMethodRequest;
use App\Http\Resources\Shipping\ShippingMethodResource;
use App\Models\ShippingMethod;
use App\Repositories\ShippingMethodRepository;
use App\Services\Shipping\ShippingMethodService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingMethodController extends Controller
{

    public function __construct(
        private ShippingMethodService $shippingMethodService,
        private ShippingMethodRepository $shippingMethodRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->shippingMethodRepository->setPagination(true);
        $this->shippingMethodRepository->setSortField(
            $request->get('sort', 'name')
        );
        $this->shippingMethodRepository->setOrderDir(
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

    public function show(ShippingMethod $shippingMethod, Request $request) {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);
        
        return new ShippingMethodResource(
            $shippingMethod
        );
    }

    public function store(StoreShippingMethodRequest $request) {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);
        
        if (!$this->shippingMethodService->createShippingMethod($request->validated())) {
            return response()->json([
                'message' => 'Error creating shipping method',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping method created',
        ], Response::HTTP_OK);
    }

    public function update(ShippingMethod $shippingMethod, UpdateShippingMethodRequest $request) {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);

        if (!$this->shippingMethodService->updateShippingMethod($shippingMethod, $request->validated())) {
            return response()->json([
                'message' => 'Error updating shipping method',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping method updated',
        ], Response::HTTP_OK);
    }
    public function destroy(ShippingMethod $shippingMethod, Request $request) {
        $this->shippingMethodService->setUser($request->user()->user);
        $this->shippingMethodService->setSite($request->user()->site);
        
        if (!$this->shippingMethodService->deleteShippingMethod($shippingMethod)) {
            return response()->json([
                'message' => 'Error deleting shipping method',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping method deleted',
        ], Response::HTTP_OK);
    }
}
