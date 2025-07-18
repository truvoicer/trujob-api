<?php

namespace App\Http\Controllers\Api\Subscription;

use App\Enums\Subscription\SubscriptionSetupFeeFailureAction;
use App\Http\Controllers\Controller;

class SubscriptionSetupFeeFailureActionController extends Controller
{

    public function index()
    {
        return response()->json([
            'message' => 'Subscription setup fee failure actions retrieved successfully',
            'data' => SubscriptionSetupFeeFailureAction::cases(),
        ]);
    }
}
