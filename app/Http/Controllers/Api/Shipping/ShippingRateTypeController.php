<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Enums\Order\Shipping\ShippingRateType;
use App\Http\Controllers\Controller;

class ShippingRateTypeController extends Controller
{


    public function index()
    {
        return response()->json([
            'message' => 'Shipping rate types retrieved successfully',
            'data' => ShippingRateType::cases(),
        ]);
    }
}
