<?php

namespace App\Http\Controllers\Api\Subscription;

use App\Enums\Subscription\SubscriptionIntervalUnit;
use App\Http\Controllers\Controller;

class SubscriptionIntervalUnitController extends Controller
{

    public function index()
    {
        return response()->json([
            'message' => 'Subscription interval units retrieved successfully',
            'data' => SubscriptionIntervalUnit::cases(),
        ]);
    }
}
