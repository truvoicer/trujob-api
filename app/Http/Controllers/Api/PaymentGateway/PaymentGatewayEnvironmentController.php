<?php

namespace App\Http\Controllers\Api\PaymentGateway;

use App\Enums\Payment\PaymentGatewayEnvironment;
use App\Http\Controllers\Controller;

class PaymentGatewayEnvironmentController extends Controller
{

    public function index()
    {
        return response()->json([
            'message' => 'Payment gateway environments retrieved successfully',
            'data' => PaymentGatewayEnvironment::cases(),
        ]);
    }
}
