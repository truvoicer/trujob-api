<?php

namespace App\Services\Payment\Stripe;

use App\Enums\Payment\PaymentGateway;
use App\Enums\Transaction\PaymentGateway\Stripe\StripeTransactionPaymentStatus;
use App\Enums\Transaction\PaymentGateway\Stripe\StripeTransactionStatus;
use App\Enums\Transaction\TransactionPaymentStatus;
use App\Enums\Transaction\TransactionStatus;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\BaseService;
use App\Services\Order\Transaction\OrderTransactionService;
use App\Services\Payment\Stripe\Middleware\Checkout\StripeCheckoutService;
use App\Services\Payment\Stripe\Middleware\StripeSubscriptionService;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeBaseOrderService extends BaseService
{


    protected StripeCheckoutService $stripeCheckoutService;
    protected OrderTransactionService $orderTransactionService;
    protected StripeSubscriptionService $stripeSubscriptionService;
    public function __construct()
    {
        // Initialize any PayPal SDK or configuration here
        parent::__construct();
        $this->stripeCheckoutService = app(StripeCheckoutService::class);
        $this->orderTransactionService = app(OrderTransactionService::class);
        $this->stripeSubscriptionService = app(StripeSubscriptionService::class);
    }

    protected function initializeStripeService(): void
    {

        $site = $this->site ?? null;

        if (!$site) {
            throw new \Exception('Site not found for the user');
        }

        $siteCurrency = $site?->settings?->currency ?? null;
        $siteLocale = $site?->settings?->locale ?? null;

        if (!$siteCurrency) {
            throw new \Exception('Site currency not found');
        }

        if (!$siteCurrency->code) {
            throw new \Exception('Site currency code not found');
        }

        if (!$siteLocale) {
            throw new \Exception('Site locale not found');
        }

        $stripePaymentGateway = $site->activePaymentGatewayByName(PaymentGateway::STRIPE)
            ->first()?->pivot ?? null;
        if (!$stripePaymentGateway) {
            throw new \Exception('Stripe payment gateway not found');
        }

        $key = $stripePaymentGateway->settings['publishable_key'] ?? null;
        $secret = $stripePaymentGateway->settings['secret_key'] ?? null;
        $success_url = $stripePaymentGateway->settings['success_url'] ?? null;
        $cancel_url = $stripePaymentGateway->settings['cancel_url'] ?? null;
        $webhook_secret = $stripePaymentGateway->settings['webhook_secret'] ?? null;

        if (!$key) {
            throw new \Exception('Stripe publishable key not found');
        }

        if (!$secret) {
            throw new \Exception('Stripe secret key not found');
        }

        // if (!$success_url) {
        //     throw new \Exception('Stripe success URL not found');
        // }

        // if (!$cancel_url) {
        //     throw new \Exception('Stripe cancel URL not found');
        // }

        $this->stripeCheckoutService->setApiKey($secret);
    }


    public function handleApproveResponse(
        Order $order, 
        Transaction $transaction, 
        Session $response
        ): Session
    {
        if (!$response) {
            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'status' => TransactionStatus::FAILED,
                    'payment_status' => TransactionPaymentStatus::UNPAID,
                    'transaction_data' => $response,
                ]
            );
            throw new \Exception(
                'Error retrieving PayPal subscription: ' . json_encode($response)
            );
        }
        $transactionStatus = null;
        $transactionPaymentStatus = null;
        switch (StripeTransactionStatus::tryFrom($response->status)) {
            case StripeTransactionStatus::COMPLETE:
                $transactionStatus = TransactionStatus::COMPLETED;
                switch (StripeTransactionPaymentStatus::tryFrom($response->payment_status)) {
                    case StripeTransactionPaymentStatus::PAID:
                        $transactionPaymentStatus = TransactionPaymentStatus::PAID;
                        break;
                    case StripeTransactionPaymentStatus::UNPAID:
                        $transactionPaymentStatus = TransactionPaymentStatus::UNPAID;
                        break;
                    case StripeTransactionPaymentStatus::NO_PAYMENT_REQUIRED:
                        $transactionPaymentStatus = TransactionPaymentStatus::NO_PAYMENT_REQUIRED;
                        break;
                    default:
                        throw new \Exception(
                            'Unknown payment status: ' . $response->payment_status
                        );
                        break;
                }
            case StripeTransactionStatus::OPEN:
                $transactionStatus = TransactionStatus::PENDING;
                break;
            case StripeTransactionStatus::EXPIRED:
                $transactionStatus = TransactionStatus::EXPIRED;
                break;
            default:
                throw new \Exception(
                    'Unknown transaction status: ' . $response->status
                );
                break;
        }
        $this->orderTransactionService->updateTransaction(
            $order,
            $transaction,
            [
                'status' => $transactionStatus ?? TransactionStatus::PENDING,
                'payment_status' => $transactionPaymentStatus ?? TransactionPaymentStatus::UNPAID,
                'transaction_data' => $response,
            ]
        );
        return $response;
    }

}
