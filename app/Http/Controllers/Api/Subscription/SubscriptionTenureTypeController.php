<?php

namespace App\Http\Controllers\Api\Subscription;

use App\Enums\Subscription\SubscriptionTenureType;
use App\Http\Controllers\Controller;

class SubscriptionTenureTypeController extends Controller
{

    public function index()
    {
        return response()->json([
            'message' => 'Subscription tenure types retrieved successfully',
            'data' => SubscriptionTenureType::cases(),
        ]);
    }
}
