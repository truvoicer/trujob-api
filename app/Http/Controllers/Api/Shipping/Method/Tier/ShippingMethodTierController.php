<?php
namespace App\Http\Controllers\Api\Shipping\Method\Tier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\Method\Tier\StoreShippingMethodTierRequest;
use App\Http\Requests\Shipping\Method\Tier\UpdateShippingMethodTierRequest;
use App\Http\Resources\Shipping\Tier\ShippingMethodTierResource;
use App\Models\ShippingMethod;
use App\Models\ShippingMethodTier;
use App\Repositories\ShippingMethodTierRepository;
use App\Services\Shipping\Tier\ShippingMethodTierService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingMethodTierController extends Controller
{
    public function __construct(
        private ShippingMethodTierService $shippingMethodTierService,
        private ShippingMethodTierRepository $shippingMethodTierRepository,
    )
    {
    }

    public function index(ShippingMethod $shippingMethod, Request $request) {
        $this->shippingMethodTierRepository->setPagination(true);
        $this->shippingMethodTierRepository->setOrderByColumn(
            $request->get('sort', 'name')
        );
        $this->shippingMethodTierRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->shippingMethodTierRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->shippingMethodTierRepository->setPage(
            $request->get('page', 1)
        );

        return ShippingMethodTierResource::collection(
            $this->shippingMethodTierRepository->findMany()
        );
    }

    public function show(ShippingMethod $shippingMethod, ShippingMethodTier $shippingMethodTier, Request $request) {
        $this->shippingMethodTierService->setUser($request->user()->user);
        $this->shippingMethodTierService->setSite($request->user()->site);

        return new ShippingMethodTierResource(
            $shippingMethodTier
        );
    }

    public function store(ShippingMethod $shippingMethod, StoreShippingMethodTierRequest $request) {
        $this->shippingMethodTierService->setUser($request->user()->user);
        $this->shippingMethodTierService->setSite($request->user()->site);

        if (!$this->shippingMethodTierService->createShippingMethodTier(
            $shippingMethod,
            $request->validated()
        )) {
            return response()->json([
                'message' => 'Error creating shipping method tier',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping method tier created',
        ], Response::HTTP_OK);
    }

    public function update(ShippingMethod $shippingMethod, ShippingMethodTier $shippingMethodTier, UpdateShippingMethodTierRequest $request) {
        $this->shippingMethodTierService->setUser($request->user()->user);
        $this->shippingMethodTierService->setSite($request->user()->site);

        if (!$this->shippingMethodTierService->updateShippingMethodTier($shippingMethodTier, $request->validated())) {
            return response()->json([
                'message' => 'Error updating shipping method tier',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping method tier updated',
        ], Response::HTTP_OK);
    }

    public function destroy(ShippingMethod $shippingMethod, ShippingMethodTier $shippingMethodTier, Request $request) {
        $this->shippingMethodTierService->setUser($request->user()->user);
        $this->shippingMethodTierService->setSite($request->user()->site);

        if (!$this->shippingMethodTierService->deleteShippingMethodTier($shippingMethodTier)) {
            return response()->json([
                'message' => 'Error deleting shipping method tier',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping method tier deleted',
        ], Response::HTTP_OK);
    }
}
