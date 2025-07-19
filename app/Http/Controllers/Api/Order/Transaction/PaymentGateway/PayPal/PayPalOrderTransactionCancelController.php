<?php

namespace App\Http\Controllers\Api\Order\Transaction\PaymentGateway\PayPal;

use App\Enums\Price\PriceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PaymentGateway\PayPal\EditPayPalOrderRequest;
use App\Http\Requests\Order\PaymentGateway\PayPal\StorePayPalOrderRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Payment\PayPal\PayPalOrderService;
use App\Services\Payment\PayPal\PayPalSubscriptionOrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayPalOrderTransactionCancelController extends Controller
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
        StorePayPalOrderRequest $request
    ) {
        $this->paypalOrderService->setUser($request->user()->user);
        $this->paypalOrderService->setSite($request->user()->site);

        $this->paypalSubscriptionOrderService->setUser($request->user()->user);
        $this->paypalSubscriptionOrderService->setSite($request->user()->site);

        switch ($order->price_type) {
            case PriceType::ONE_TIME:
                $handleCancellation = $this->paypalOrderService->handleOrderCancellation(
                    $order,
                    $transaction,
                    $request->validated()
                );
                if (!$handleCancellation) {
                    return $this->sendJsonResponse(
                        true,
                        'Error cancelling PayPal order',
                        null,
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                return $this->sendJsonResponse(
                    true,
                    'PayPal order cancelled successfully',
                    [],
                    Response::HTTP_OK
                );
                break;
            case PriceType::SUBSCRIPTION:
                $handleCancellation = $this->paypalSubscriptionOrderService->handleSubscriptionCancel(
                    $order,
                    $transaction,
                    $request->validated()
                );
                if (!$handleCancellation) {
                    return $this->sendJsonResponse(
                        true,
                        'Error handling PayPal subscription cancellation',
                        null,
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                return $this->sendJsonResponse(
                    true,
                    'PayPal subscription cancelled successfully',
                    [],
                    Response::HTTP_OK
                );
                break;
            default:
                return response()->json([
                    'message' => 'Invalid price type',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
