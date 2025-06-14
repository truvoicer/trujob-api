<?php

namespace App\Http\Controllers\Api\Shipping\Method\Restriction;

use App\Enums\Order\Shipping\ShippingRestrictionType;
use App\Http\Controllers\Controller;

class ShippingRestrictionTypeController extends Controller
{


    public function index()
    {
        return response()->json([
            'message' => 'Shipping restriction types retrieved successfully',
            'data' => ShippingRestrictionType::cases(),
        ]);
    }
}
