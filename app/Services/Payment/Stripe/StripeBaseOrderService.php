<?php

namespace App\Services\Payment\Stripe;

use App\Enums\Payment\PaymentGateway;
use App\Services\BaseService;
use App\Services\Order\Transaction\OrderTransactionService;
use App\Services\Payment\Stripe\Middleware\Checkout\StripeCheckoutService;
use App\Services\Payment\Stripe\Middleware\StripeSubscriptionService;
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

        if (!$success_url) {
            throw new \Exception('Stripe success URL not found');
        }

        if (!$cancel_url) {
            throw new \Exception('Stripe cancel URL not found');
        }

        $this->stripeCheckoutService->setApiKey($secret);
    }

}
