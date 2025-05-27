<?php

namespace App\Http\Controllers\Api\ShippingRate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\Rate\StoreShippingRateRequest;
use App\Http\Requests\Shipping\Rate\UpdateShippingRateRequest;
use App\Http\Resources\Shipping\ShippingRateResource;
use App\Models\ShippingRate;
use App\Repositories\ShippingRateRepository;
use App\Services\Shipping\ShippingRateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingRateController extends Controller
{

    public function __construct(
        private ShippingRateService $shippingRateService,
        private ShippingRateRepository $shippingRateRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->shippingRateRepository->setPagination(true);
        $this->shippingRateRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->shippingRateRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->shippingRateRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->shippingRateRepository->setPage(
            $request->get('page', 1)
        );
        
        return ShippingRateResource::collection(
            $this->shippingRateRepository->findMany()
        );
    }

    public function create(StoreShippingRateRequest $request) {
        $this->shippingRateService->setUser($request->user()->user);
        $this->shippingRateService->setSite($request->user()->site);
        
        if (!$this->shippingRateService->createShippingRate($request->validated())) {
            return response()->json([
                'message' => 'Error creating shipping rate',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping rate created',
        ], Response::HTTP_OK);
    }

    public function update(ShippingRate $shippingRate, UpdateShippingRateRequest $request) {
        $this->shippingRateService->setUser($request->user()->user);
        $this->shippingRateService->setSite($request->user()->site);

        if (!$this->shippingRateService->updateShippingRate($shippingRate, $request->validated())) {
            return response()->json([
                'message' => 'Error updating shipping rate',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping rate updated',
        ], Response::HTTP_OK);
    }
    public function destroy(ShippingRate $shippingRate, Request $request) {
        $this->shippingRateService->setUser($request->user()->user);
        $this->shippingRateService->setSite($request->user()->site);
        
        if (!$this->shippingRateService->deleteShippingRate($shippingRate)) {
            return response()->json([
                'message' => 'Error deleting shipping rate',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping rate deleted',
        ], Response::HTTP_OK);
    }
}
