<?php

namespace App\Http\Controllers\Api\Shipping\Zone;

use App\Enums\Order\Shipping\ShippingZoneAbleType;
use App\Http\Controllers\Controller;

class ShippingZoneableTypeController extends Controller
{

    public function index()
    {
        return response()->json([
            'message' => 'Shipping zone types retrieved successfully',
            'data' => ShippingZoneAbleType::cases(),
        ]);
    }
}
