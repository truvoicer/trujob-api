<?php

namespace App\Http\Controllers\Api\Discount;

use App\Enums\Order\Discount\DiscountAmountType;
use App\Http\Controllers\Controller;
use App\Repositories\DiscountRepository;
use App\Services\Discount\DiscountService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscountAmountTypeController extends Controller
{

    public function __construct(
        private DiscountService $discountService,
        private DiscountRepository $discountRepository,
    ) {}


    public function __invoke(Request $request)
    {

        return response()->json([
            'data' => DiscountAmountType::cases(),
        ], Response::HTTP_OK);
    }
}
