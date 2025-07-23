<?php

namespace App\Http\Controllers\Api\Order\Transaction\PaymentGateway\Stripe;

use App\Enums\Price\PriceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PaymentGateway\Stripe\StoreStripeCheckoutSessionApproveRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Payment\Stripe\StripeOrderService;
use App\Services\Payment\Stripe\StripeSubscriptionOrderService;
use Symfony\Component\HttpFoundation\Response;

class StripeOrderCheckoutSessionApproveController extends Controller
{

    public function __construct(
        private StripeOrderService $stripeOrderService,
        private StripeSubscriptionOrderService $stripeSubscriptionOrderService,
    ) {
        parent::__construct();
    }

    public function store(
        Order $order,
        Transaction $transaction,
        StoreStripeCheckoutSessionApproveRequest $request
    ) {
        $this->stripeOrderService->setUser($request->user()->user);
        $this->stripeOrderService->setSite($request->user()->site);
        $this->stripeSubscriptionOrderService->setUser($request->user()->user);
        $this->stripeSubscriptionOrderService->setSite($request->user()->site);


        switch ($order->price_type) {
            case PriceType::ONE_TIME:
                $createOrder = $this->stripeOrderService->handleOneTimePaymentApproval(
                    $order,
                    $transaction,
                    $request->validated()
                );
                if (!$createOrder) {
                    return $this->sendJsonResponse(
                        true,
                        'Error creating Stripe checkout session',
                        null,
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                return $this->sendJsonResponse(
                    true,
                    'Stripe checkout session created',
                    [
                        'id' => $createOrder->id,
                        'status' => $createOrder->status,
                        'payment_status' => $createOrder->payment_status,
                    ],
                    Response::HTTP_CREATED
                );
            case PriceType::SUBSCRIPTION:

                $approveSubscription = $this->stripeSubscriptionOrderService->handleSubscriptionApproval(
                    $order,
                    $transaction,
                    $request->validated()
                );
                if (!$approveSubscription) {
                    return $this->sendJsonResponse(
                        true,
                        'Error creating Stripe subscription checkout session',
                        null,
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                return $this->sendJsonResponse(
                    true,
                    'Stripe subscription checkout session approved',
                    [
                        'id' => $approveSubscription->id,
                        'status' => $approveSubscription->status,
                        'payment_status' => $approveSubscription->payment_status,
                    ],
                    Response::HTTP_CREATED
                );
            default:
                return response()->json([
                    'message' => 'Invalid price type',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
