<?php

namespace App\Http\Controllers\Api\Order\Transaction\PaymentGateway\Stripe;

use App\Enums\Price\PriceType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\PaymentGateway\Stripe\StoreStripeCheckoutSessionRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Payment\Stripe\StripeOrderService;
use App\Services\Payment\Stripe\StripeSubscriptionOrderService;
use Symfony\Component\HttpFoundation\Response;

class StripeOrderCheckoutSessionCancelController extends Controller
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
        StoreStripeCheckoutSessionRequest $request
    ) {
        $this->stripeOrderService->setUser($request->user()->user);
        $this->stripeOrderService->setSite($request->user()->site);
        $this->stripeSubscriptionOrderService->setUser($request->user()->user);
        $this->stripeSubscriptionOrderService->setSite($request->user()->site);


        switch ($order->price_type) {
            case PriceType::ONE_TIME:
                $createOrder = $this->stripeOrderService->createCheckoutSession(
                    $order,
                    $transaction
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
                        'client_secret' => $createOrder->client_secret,
                    ],
                    Response::HTTP_CREATED
                );
            case PriceType::SUBSCRIPTION:

                $createOrder = $this->stripeSubscriptionOrderService->createSubscription(
                    $order,
                    $transaction
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
                        'client_secret' => $createOrder->client_secret,
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
