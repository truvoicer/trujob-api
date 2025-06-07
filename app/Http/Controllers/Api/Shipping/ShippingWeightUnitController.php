<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Enums\Order\Shipping\ShippingWeightUnit;
use App\Http\Controllers\Controller;

class ShippingWeightUnitController extends Controller
{


    public function index()
    {
        return response()->json([
            'message' => 'Shipping weight units retrieved successfully',
            'data' => ShippingWeightUnit::cases(),
        ]);
    }
}
