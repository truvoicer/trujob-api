<?php

namespace App\Http\Controllers\Api\Order\Transaction\PaymentGateway\PayPal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PaymentGateway\PayPal\EditPayPalOrderRequest;
use App\Http\Requests\Order\PaymentGateway\PayPal\StorePayPalOrderRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Payment\PayPal\PayPalOrderService;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayPalOrderTransactionController extends Controller
{

    public function __construct(
        private PayPalOrderService $paypalOrderService,
    ) {
        parent::__construct();
    }

    public function show(Order $order, Transaction $transaction, Request $request)
    {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);
        return response()->json([
            'message' => 'Order retrieved successfully',
        ], Response::HTTP_OK);
    }

    public function store(Order $order, Transaction $transaction, StorePayPalOrderRequest $request)
    {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);
        $createOrder = $this->paypalOrderService->createOrder(
            $order,
            $transaction
        );
        if (!$createOrder) {
            return $this->sendJsonResponse(
                true,
                'Error creating PayPal order',
                null,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->sendJsonResponse(
            true,
            'PayPal order created',
            $createOrder,
            Response::HTTP_CREATED
        );
    }

    public function update(Order $order, Transaction $transaction, EditPayPalOrderRequest $request)
    {
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

    public function destroy(Order $order, Transaction $transaction, Request $request)
    {
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
