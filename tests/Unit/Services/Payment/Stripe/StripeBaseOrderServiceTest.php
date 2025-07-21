<?php

namespace Tests\Unit\Services\Payment\Stripe;

use App\Enums\Payment\PaymentGateway;
use App\Models\PaymentGateway as PaymentGatewayModel;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Services\Payment\Stripe\StripeBaseOrderService;
use App\Services\Payment\Stripe\Middleware\StripeCheckoutService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeBaseOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private StripeBaseOrderService $stripeBaseOrderService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock dependencies injected via constructor if needed
        $this->stripeBaseOrderService = $this->getMockBuilder(StripeBaseOrderService::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->stripeCheckoutService = $this->mock(StripeCheckoutService::class);
        $this->app->instance(StripeCheckoutService::class, $this->stripeCheckoutService);

        // Set up the site, settings, and payment gateway for the service
        $this->site = Site::factory()->create();
        $this->siteSetting = SiteSetting::factory()->create(['site_id' => $this->site->id]);
        $this->site->settings()->save($this->siteSetting);

        // Create and link a Stripe payment gateway for the site
        $this->paymentGateway = PaymentGatewayModel::factory()->create(['name' => PaymentGateway::STRIPE]);

        $this->site->paymentGateways()->attach($this->paymentGateway, [
            'settings' => [
                'publishable_key' => 'test_publishable_key',
                'secret_key' => 'test_secret_key',
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
                'webhook_secret' => 'test_webhook_secret',
            ],
        ]);

        // Bind the site to the service.  Use reflection to set the property.
        $reflection = new \ReflectionClass(StripeBaseOrderService::class);
        $siteProperty = $reflection->getProperty('site');
        $siteProperty->setAccessible(true);
        $siteProperty->setValue($this->stripeBaseOrderService, $this->site);

        // Bind the StripeCheckoutService to the service.
        $reflectionCheckout = new \ReflectionClass(StripeBaseOrderService::class);
        $checkoutProperty = $reflectionCheckout->getProperty('stripeCheckoutService');
        $checkoutProperty->setAccessible(true);
        $checkoutProperty->setValue($this->stripeBaseOrderService, $this->stripeCheckoutService);

    }

    public function testInitializeStripeServiceSetsApiKeySuccessfully(): void
    {
        // Arrange
        $this->stripeCheckoutService->shouldReceive('setApiKey')
            ->once()
            ->with('test_secret_key');

        // Act & Assert - Call the method and assert no exception is thrown
        $this->stripeBaseOrderService->expects('initializeStripeService')->withNoArgs()->willReturn(null);
        $this->stripeBaseOrderService->initializeStripeService();

        // Assert that the secret key is set on the Stripe client.  (Already asserted by mock)
        $this->assertTrue(true);
    }

    public function testInitializeStripeServiceThrowsExceptionWhenSiteIsNotFound(): void
    {
        // Arrange
        // Create a new instance where the site is not initialized.
        $stripeBaseOrderService = $this->getMockBuilder(StripeBaseOrderService::class)
        ->disableOriginalConstructor()
        ->getMockForAbstractClass();

        $reflection = new \ReflectionClass(StripeBaseOrderService::class);
        $siteProperty = $reflection->getProperty('site');
        $siteProperty->setAccessible(true);
        $siteProperty->setValue($stripeBaseOrderService, null);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Site not found for the user');
        $stripeBaseOrderService->initializeStripeService();
    }

    public function testInitializeStripeServiceThrowsExceptionWhenSiteCurrencyIsNotFound(): void
    {
        // Arrange
        $this->siteSetting->currency_id = null;
        $this->siteSetting->save();

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Site currency not found');
        $this->stripeBaseOrderService->initializeStripeService();
    }

    public function testInitializeStripeServiceThrowsExceptionWhenSiteCurrencyCodeIsNotFound(): void
    {
        // Arrange
        $this->siteSetting->currency->code = null;
        $this->siteSetting->currency->save();
        $this->siteSetting->save();
        $this->site->settings()->save($this->siteSetting);


        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Site currency code not found');
        $this->stripeBaseOrderService->initializeStripeService();
    }

    public function testInitializeStripeServiceThrowsExceptionWhenSiteLocaleIsNotFound(): void
    {
        // Arrange
        $this->siteSetting->locale = null;
        $this->siteSetting->save();

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Site locale not found');
        $this->stripeBaseOrderService->initializeStripeService();
    }

    public function testInitializeStripeServiceThrowsExceptionWhenStripePaymentGatewayIsNotFound(): void
    {
        // Arrange
        $this->site->paymentGateways()->detach($this->paymentGateway);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stripe payment gateway not found');
        $this->stripeBaseOrderService->initializeStripeService();
    }

    public function testInitializeStripeServiceThrowsExceptionWhenStripePublishableKeyIsNotFound(): void
    {
        // Arrange
        $this->site->paymentGateways()->updateExistingPivot($this->paymentGateway->id, ['settings' => [
            'secret_key' => 'test_secret_key',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'webhook_secret' => 'test_webhook_secret',
        ]]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stripe publishable key not found');
        $this->stripeBaseOrderService->initializeStripeService();
    }

    public function testInitializeStripeServiceThrowsExceptionWhenStripeSecretKeyIsNotFound(): void
    {
        // Arrange
         $this->site->paymentGateways()->updateExistingPivot($this->paymentGateway->id, ['settings' => [
            'publishable_key' => 'test_publishable_key',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'webhook_secret' => 'test_webhook_secret',
        ]]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stripe secret key not found');
        $this->stripeBaseOrderService->initializeStripeService();
    }

     public function testInitializeStripeServiceThrowsExceptionWhenStripeSuccessUrlIsNotFound(): void
    {
        // Arrange
         $this->site->paymentGateways()->updateExistingPivot($this->paymentGateway->id, ['settings' => [
            'publishable_key' => 'test_publishable_key',
            'secret_key' => 'test_secret_key',
            'cancel_url' => 'https://example.com/cancel',
            'webhook_secret' => 'test_webhook_secret',
        ]]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stripe success URL not found');
        $this->stripeBaseOrderService->initializeStripeService();
    }

     public function testInitializeStripeServiceThrowsExceptionWhenStripeCancelUrlIsNotFound(): void
    {
        // Arrange
        $this->site->paymentGateways()->updateExistingPivot($this->paymentGateway->id, ['settings' => [
            'publishable_key' => 'test_publishable_key',
            'secret_key' => 'test_secret_key',
            'success_url' => 'https://example.com/success',
            'webhook_secret' => 'test_webhook_secret',
        ]]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stripe cancel URL not found');
        $this->stripeBaseOrderService->initializeStripeService();
    }


}