<?php

namespace App\Http\Controllers\Api\PaymentGateway\PayPal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PaymentGateway\PayPal\EditPayPalOrderRequest;
use App\Http\Requests\Order\PaymentGateway\PayPal\StorePayPalOrderRequest;
use App\Models\Order;
use App\Services\Payment\PayPal\PayPalOrderService;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayPalOrderController extends Controller
{

    public function __construct(
        private PayPalOrderService $paypalOrderService,
    )
    {
        parent::__construct();
    }
    public function index(Order $order, Request $request)
    {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);

        return response()->json([
            'message' => 'Orders retrieved successfully',
        ], Response::HTTP_OK);
    }

}
