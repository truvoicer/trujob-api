<?php

namespace App\Http\Controllers\Api\Discount;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Repositories\DiscountRepository;
use App\Services\Discount\DiscountService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscountSetAsDefaultController extends Controller
{

    public function __construct(
        private DiscountService $discountService,
        private DiscountRepository $discountRepository,
    ) {}


    public function store(Discount $discount, Request $request)
    {
        $this->discountService->setUser($request->user()->user);
        $this->discountService->setSite($request->user()->site);

        $this->discountService->setAsDefault($discount);

        return response()->json([
            'message' => 'Discount set as default',
        ], Response::HTTP_OK);
    }

    public function destroy(Discount $discount, Request $request)
    {
        $this->discountService->setUser($request->user()->user);
        $this->discountService->setSite($request->user()->site);

        $this->discountService->removeAsDefault($discount);
        return response()->json([
            'message' => 'Discount removed as default',
        ], Response::HTTP_OK);
    }

}
