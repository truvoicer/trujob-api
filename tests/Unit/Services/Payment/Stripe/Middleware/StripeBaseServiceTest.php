<?php

namespace Tests\Unit\Services\Payment\Stripe\Middleware;

use App\Services\Payment\Stripe\Middleware\StripeBaseService;
use App\Services\Payment\Stripe\Middleware\StripeResponse;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Tests\TestCase;

class StripeBaseServiceTest extends TestCase
{
    private StripeBaseService $stripeBaseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stripeBaseService = new StripeBaseService();
    }

    public function testSetApiKey(): void
    {
        $apiKey = 'test_api_key';
        $this->stripeBaseService->setApiKey($apiKey);
        $this->assertEquals($apiKey, $this->stripeBaseService->getApiKey());
    }

    public function testGetApiKey(): void
    {
        $apiKey = 'test_api_key';
        $this->stripeBaseService->setApiKey($apiKey);
        $this->assertEquals($apiKey, $this->stripeBaseService->getApiKey());
    }

    public function testGetClient(): void
    {
        $this->stripeBaseService->setApiKey('test_api_key');
        $reflection = new \ReflectionClass(StripeBaseService::class);
        $method = $reflection->getMethod('initializeStripeClient');
        $method->setAccessible(true);

        $method->invoke($this->stripeBaseService);

        $this->assertInstanceOf(StripeClient::class, $this->stripeBaseService->getClient());
    }

    public function testSetCurrencyCode(): void
    {
        $currencyCode = 'USD';
        $this->stripeBaseService->setCurrencyCode($currencyCode);
        $this->assertEquals($currencyCode, $this->stripeBaseService->getCurrencyCode());
    }

    public function testGetCurrencyCode(): void
    {
        $currencyCode = 'USD';
        $this->stripeBaseService->setCurrencyCode($currencyCode);
        $this->assertEquals($currencyCode, $this->stripeBaseService->getCurrencyCode());
    }

    public function testSetLocale(): void
    {
        $locale = 'en_US';
        $this->stripeBaseService->setLocale($locale);
        $this->assertEquals($locale, $this->stripeBaseService->getLocale());
    }

    public function testGetLocale(): void
    {
        $locale = 'en_US';
        $this->stripeBaseService->setLocale($locale);
        $this->assertEquals($locale, $this->stripeBaseService->getLocale());
    }

    public function testInitializeStripeClient(): void
    {
        $this->stripeBaseService->setApiKey('test_api_key');

        $reflection = new \ReflectionClass(StripeBaseService::class);
        $method = $reflection->getMethod('initializeStripeClient');
        $method->setAccessible(true);

        $method->invoke($this->stripeBaseService);
        $this->assertInstanceOf(StripeClient::class, $this->stripeBaseService->getClient());
    }

    public function testInitializeStripeClientThrowsExceptionIfApiKeyNotSet(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stripe API key is not set.');

        $reflection = new \ReflectionClass(StripeBaseService::class);
        $method = $reflection->getMethod('initializeStripeClient');
        $method->setAccessible(true);

        $method->invoke($this->stripeBaseService);
    }

    public function testCallStripeApiSuccessfulCall(): void
    {
        $apiKey = 'test_api_key';
        $this->stripeBaseService->setApiKey($apiKey);

        $result = $this->stripeBaseService->callStripeApi(function () {
            // Mock Stripe API call here - Replace with a mock API call that returns a known value
            // For example, using Mockery:
            // $mockStripeClient = \Mockery::mock(\Stripe\StripeClient::class);
            // $mockStripeClient->shouldReceive('someMethod')->once()->andReturn('mocked_result');
            // $this->stripeBaseService->stripeClient = $mockStripeClient;

            // For now, just return a simple string for demonstration
            return 'success';
        });

        $this->assertEquals('success', $result);
    }

    public function testCallStripeApiHandlesApiErrorException(): void
    {
        $this->stripeBaseService->setApiKey('test_api_key');

        Log::shouldReceive('error')->once();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stripe API Error: Test API Error');

        $this->stripeBaseService->callStripeApi(function () {
            throw new ApiErrorException('Test API Error', 400);
        });
    }

    public function testCallStripeApiHandlesGeneralException(): void
    {
        $this->stripeBaseService->setApiKey('test_api_key');

        Log::shouldReceive('error')->once();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('An unexpected error occurred: Test Error');

        $this->stripeBaseService->callStripeApi(function () {
            throw new \Exception('Test Error');
        });
    }
}