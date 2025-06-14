<?php

namespace App\Http\Controllers\Api\Shipping\Method\Rate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\Rate\StoreShippingRateRequest;
use App\Http\Requests\Shipping\Rate\UpdateShippingRateRequest;
use App\Http\Resources\Shipping\ShippingRateResource;
use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Repositories\ShippingRateRepository;
use App\Services\Shipping\ShippingRateService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingMethodRateController extends Controller
{

    public function __construct(
        private ShippingRateService $shippingRateService,
        private ShippingRateRepository $shippingRateRepository,
    )
    {
    }

    public function index(ShippingMethod $shippingMethod, Request $request) {
        $this->shippingRateRepository->setQuery(
            $shippingMethod->rates()
        );
        $this->shippingRateRepository->setPagination(true);
        $this->shippingRateRepository->setOrderByColumn(
            $request->get('sort', 'created_at')
        );
        $this->shippingRateRepository->setOrderByDir(
            $request->get('order', 'desc')
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

    public function show(ShippingMethod $shippingMethod, ShippingRate $shippingRate, Request $request) {
        $this->shippingRateService->setUser($request->user()->user);
        $this->shippingRateService->setSite($request->user()->site);
        if (!$shippingRate->shippingMethod->is($shippingMethod)) {
            return response()->json([
                'message' => 'Shipping rate does not belong to this shipping method',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return new ShippingRateResource(
            $shippingRate
        );
    }

    public function store(ShippingMethod $shippingMethod, StoreShippingRateRequest $request) {
        $this->shippingRateService->setUser($request->user()->user);
        $this->shippingRateService->setSite($request->user()->site);

        if (!$this->shippingRateService->createShippingRate($shippingMethod, $request->validated())) {
            return response()->json([
                'message' => 'Error creating shipping rate',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping rate created',
        ], Response::HTTP_OK);
    }

    public function update(ShippingMethod $shippingMethod, ShippingRate $shippingRate, UpdateShippingRateRequest $request) {
        $this->shippingRateService->setUser($request->user()->user);
        $this->shippingRateService->setSite($request->user()->site);

        if (!$shippingRate->shippingMethod->is($shippingMethod)) {
            return response()->json([
                'message' => 'Shipping rate does not belong to this shipping method',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (!$this->shippingRateService->updateShippingRate($shippingRate, $request->validated())) {
            return response()->json([
                'message' => 'Error updating shipping rate',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping rate updated',
        ], Response::HTTP_OK);
    }
    public function destroy(ShippingMethod $shippingMethod, ShippingRate $shippingRate, Request $request) {
        $this->shippingRateService->setUser($request->user()->user);
        $this->shippingRateService->setSite($request->user()->site);

        if (!$shippingRate->shippingMethod->is($shippingMethod)) {
            return response()->json([
                'message' => 'Shipping rate does not belong to this shipping method',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
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
