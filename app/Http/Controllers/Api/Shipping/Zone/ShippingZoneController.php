<?php

namespace App\Http\Controllers\Api\Shipping\Zone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\Zone\StoreShippingZoneRequest;
use App\Http\Requests\Shipping\Zone\UpdateShippingZoneRequest;
use App\Http\Resources\Shipping\ShippingZoneResource;
use App\Models\ShippingZone;
use App\Repositories\ShippingZoneRepository;
use App\Services\Shipping\ShippingZoneService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingZoneController extends Controller
{

    public function __construct(
        private ShippingZoneService $shippingZoneService,
        private ShippingZoneRepository $shippingZoneRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->shippingZoneRepository->setPagination(true);
        $this->shippingZoneRepository->setOrderByColumn(
            $request->get('sort', 'name')
        );
        $this->shippingZoneRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->shippingZoneRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->shippingZoneRepository->setPage(
            $request->get('page', 1)
        );

        return ShippingZoneResource::collection(
            $this->shippingZoneRepository->findMany()
        );
    }

    public function show(ShippingZone $shippingZone, Request $request) {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);

        return new ShippingZoneResource(
            $shippingZone
        );
    }

    public function store(StoreShippingZoneRequest $request) {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);

        if (!$this->shippingZoneService->createShippingZone($request->validated())) {
            return response()->json([
                'message' => 'Error creating shipping zone',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping zone created',
        ], Response::HTTP_OK);
    }

    public function update(ShippingZone $shippingZone, UpdateShippingZoneRequest $request) {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);

        if (!$this->shippingZoneService->updateShippingZone($shippingZone, $request->validated())) {
            return response()->json([
                'message' => 'Error updating shipping zone',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping zone updated',
        ], Response::HTTP_OK);
    }
    public function destroy(ShippingZone $shippingZone, Request $request) {
        $this->shippingZoneService->setUser($request->user()->user);
        $this->shippingZoneService->setSite($request->user()->site);

        if (!$this->shippingZoneService->deleteShippingZone($shippingZone)) {
            return response()->json([
                'message' => 'Error deleting shipping zone',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping zone deleted',
        ], Response::HTTP_OK);
    }
}
