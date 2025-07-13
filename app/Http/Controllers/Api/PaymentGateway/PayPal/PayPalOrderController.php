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
    }
    public function index(Order $order, Request $request)
    {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);

        return response()->json([
            'message' => 'Orders retrieved successfully',
        ], Response::HTTP_OK);
    }

    public function show(Order $order, string $paypalOrderId, Request $request) {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);
        return response()->json([
            'message' => 'Order retrieved successfully',
        ], Response::HTTP_OK);
    }

    public function store(Order $order, StorePayPalOrderRequest $request) {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);

        if (!$this->paypalOrderService->createOrder($order)) {
            return response()->json([
                'message' => 'Error creating paymentGateway',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'PaymentGateway created',
        ], Response::HTTP_CREATED);
    }

    public function update(Order $order, EditPayPalOrderRequest $request) {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);

        // if (!$this->paypalOrderService->updatePaymentGateway($paymentGateway, $request->validated())) {
        //     return response()->json([
        //         'message' => 'Error updating paymentGateway',
        //     ], Response::HTTP_UNPROCESSABLE_ENTITY);
        // }
        return response()->json([
            'message' => 'PaymentGateway updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Order $order, Request $request) {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);

        // if (!$this->paypalOrderService->deletePaymentGateway($paymentGateway)) {
        //     return response()->json([
        //         'message' => 'Error deleting paymentGateway',
        //     ], Response::HTTP_UNPROCESSABLE_ENTITY);
        // }
        return response()->json([
            'message' => 'PaymentGateway deleted',
        ], Response::HTTP_OK);
    }

}
