<?php

namespace App\Http\Controllers\Api\Order\Transaction\PaymentGateway\PayPal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PaymentGateway\PayPal\Capture\StorePayPalOrderCaptureRequest;

use App\Http\Requests\Order\PaymentGateway\PayPal\StorePayPalOrderRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Payment\PayPal\PayPalOrderService;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayPalOrderTransactionCaptureController extends Controller
{

    public function __construct(
        private PayPalOrderService $paypalOrderService,
    ) {
        parent::__construct();
    }


    public function store(
        Order $order,
        Transaction $transaction,
        StorePayPalOrderCaptureRequest $request
    ) {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);
        $createOrder = $this->paypalOrderService->captureOrder(
            $order,
            $transaction,
            $request->validated('order_id')
        );
        if (!$createOrder) {
            return $this->sendJsonResponse(
                true,
                'Error capturing PayPal order',
                null,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->sendJsonResponse(
            true,
            'PayPal order captured',
            $createOrder,
            Response::HTTP_CREATED
        );
    }
}
