<?php

namespace App\Http\Controllers\Api\ShippingRestriction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\Restriction\StoreShippingRestrictionRequest;
use App\Http\Requests\Shipping\Restriction\UpdateShippingRestrictionRequest;
use App\Http\Resources\Shipping\ShippingRestrictionResource;
use App\Models\ShippingRestriction;
use App\Repositories\ShippingRestrictionRepository;
use App\Services\Shipping\ShippingRestrictionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingRestrictionController extends Controller
{

    public function __construct(
        private ShippingRestrictionService $shippingRestrictionService,
        private ShippingRestrictionRepository $shippingRestrictionRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->shippingRestrictionRepository->setPagination(true);
        $this->shippingRestrictionRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->shippingRestrictionRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->shippingRestrictionRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->shippingRestrictionRepository->setPage(
            $request->get('page', 1)
        );
        
        return ShippingRestrictionResource::collection(
            $this->shippingRestrictionRepository->findMany()
        );
    }

    public function create(StoreShippingRestrictionRequest $request) {
        $this->shippingRestrictionService->setUser($request->user()->user);
        $this->shippingRestrictionService->setSite($request->user()->site);
        $validated = request()->validate([
            'type' => 'required|in:product,category,location',
            'restriction_id' => 'required|integer',
            'action' => 'required|in:allow,deny',
        ]);
        $type = $request->validated('type');
        // Validate restriction_id based on type
        if ($type === 'product') {
            request()->validate(['restriction_id' => 'exists:products,id']);
        } elseif ($type === 'category') {
            request()->validate(['restriction_id' => 'exists:categories,id']);
        }

        if (!$this->shippingRestrictionService->createShippingRestriction($request->validated())) {
            return response()->json([
                'message' => 'Error creating shipping restriction',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping restriction created',
        ], Response::HTTP_OK);
    }

    public function update(ShippingRestriction $shippingRestriction, UpdateShippingRestrictionRequest $request) {
        $this->shippingRestrictionService->setUser($request->user()->user);
        $this->shippingRestrictionService->setSite($request->user()->site);

        $type = $request->validated('type');
        // Validate restriction_id based on type
        if ($type === 'product') {
            request()->validate(['restriction_id' => 'exists:products,id']);
        } elseif ($type === 'category') {
            request()->validate(['restriction_id' => 'exists:categories,id']);
        }
        if (!$this->shippingRestrictionService->updateShippingRestriction($shippingRestriction, $request->validated())) {
            return response()->json([
                'message' => 'Error updating shipping restriction',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping restriction updated',
        ], Response::HTTP_OK);
    }
    public function destroy(ShippingRestriction $shippingRestriction, Request $request) {
        $this->shippingRestrictionService->setUser($request->user()->user);
        $this->shippingRestrictionService->setSite($request->user()->site);
        
        if (!$this->shippingRestrictionService->deleteShippingRestriction($shippingRestriction)) {
            return response()->json([
                'message' => 'Error deleting shipping restriction',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Shipping restriction deleted',
        ], Response::HTTP_OK);
    }
}
