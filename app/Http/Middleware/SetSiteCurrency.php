<?php

namespace App\Http\Middleware;

use App\Enums\Payment\PaymentGateway;
use Closure;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use App\Models\Site; // Your Site model

class SetSiteCurrency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $site = request()->user()?->site ?? null;

        if (!$site) {
            return $next($request);
        }

        $siteCurrency = $site?->settings?->currency ?? null;
        dd($siteCurrency);
        if (!$siteCurrency) {
            return $next($request);
        }

        if (!$siteCurrency->code) {
            return $next($request);
        }

        if (!$siteCurrency->locale) {
            return $next($request);
        }

        $stripePaymentGateway = $site->activePaymentGatewayByName(PaymentGateway::STRIPE)
            ->first()?->pivot ?? null;
        if (!$stripePaymentGateway) {
            return $next($request);
        }

        $key = $stripePaymentGateway->settings['publishable_key'] ?? null;
        $secret = $stripePaymentGateway->settings['secret_key'] ?? null;
        if (!$key) {
            return $next($request);
        }

        if (!$secret) {
            return $next($request);
        }

        // Set the currency and locale for Cashier
        config(['cashier.currency' => $siteCurrency->code]);
        config(['cashier.currency_locale' => $siteCurrency->locale]);
        config(['cashier.key' => $key]);
        config(['cashier.secret' => $secret]);
        return $next($request);
    }
}
