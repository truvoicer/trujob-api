<?php

namespace App\Http\Controllers\Api\Shipping\Method\Restriction;

use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Http\Controllers\Controller;

class ShippingRestrictionActionController extends Controller
{


    public function index()
    {
        return response()->json([
            'message' => 'Shipping restriction actions retrieved successfully',
            'data' => ShippingRestrictionAction::cases(),
        ]);
    }
}
