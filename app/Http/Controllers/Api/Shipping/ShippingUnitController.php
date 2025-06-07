<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Enums\Order\Shipping\ShippingUnit;
use App\Http\Controllers\Controller;

class ShippingUnitController extends Controller
{


    public function index()
    {
        return response()->json([
            'message' => 'Shipping unit types retrieved successfully',
            'data' => ShippingUnit::cases(),
        ]);
    }
}
