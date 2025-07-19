<?php

namespace App\Http\Controllers\Api\Order\Transaction\PaymentGateway\PayPal;

use App\Enums\Price\PriceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PaymentGateway\PayPal\Approve\StorePayPalOrderApproveRequest;
use App\Http\Requests\Order\PaymentGateway\PayPal\StorePayPalOrderRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Payment\PayPal\PayPalOrderService;
use App\Services\Payment\PayPal\PayPalSubscriptionOrderService;
use Symfony\Component\HttpFoundation\Response;

class PayPalOrderTransactionApproveController extends Controller
{

    public function __construct(
        private PayPalOrderService $paypalOrderService,
        private PayPalSubscriptionOrderService $paypalSubscriptionOrderService,
    ) {
        parent::__construct();
    }

    public function store(
        Order $order,
        Transaction $transaction,
        StorePayPalOrderApproveRequest $request
    ) {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);

        $this->paypalSubscriptionOrderService->setUser($request->user()->user);
        $this->paypalSubscriptionOrderService->setSite($request->user()->site);

        switch ($order->price_type) {
            case PriceType::SUBSCRIPTION:
                return $this->sendJsonResponse(
                    true,
                    'PayPal subscription order approved',
                    $this->paypalSubscriptionOrderService->handleSubscriptionApproval(
                        $order,
                        $transaction,
                        $request->validated()
                    )->getResponseData(),
                    Response::HTTP_CREATED
                );
                break;
            default:
                return response()->json([
                    'message' => 'Invalid price type',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
