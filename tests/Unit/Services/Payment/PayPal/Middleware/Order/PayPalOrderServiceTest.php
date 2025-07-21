<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware\Order;

use App\Enums\Payment\PaymentGatewayEnvironment;
use App\Services\Payment\PayPal\Middleware\Order\PayPalOrderService;
use App\Services\Payment\PayPal\Middleware\Order\PayPalOrderResponseHandler;
use Exception;
use PaypalServerSdkLib\Models\Item;
use PaypalServerSdkLib\PaypalServerSdkClient;
use Tests\TestCase;

class PayPalOrderServiceTest extends TestCase
{
    private PayPalOrderService $payPalOrderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payPalOrderService = new PayPalOrderService();
    }

    public function testSetAndGetDiscount(): void
    {
        $discount = 10.50;
        $this->payPalOrderService->setDiscount($discount);
        $this->assertEquals($discount, $this->payPalOrderService->getDiscount());
    }

    public function testSetAndGetItemTotal(): void
    {
        $itemTotal = 100.00;
        $this->payPalOrderService->setItemTotal($itemTotal);
        $this->assertEquals($itemTotal, $this->payPalOrderService->getItemTotal());
    }

    public function testSetAndGetTaxTotal(): void
    {
        $taxTotal = 5.00;
        $this->payPalOrderService->setTaxTotal($taxTotal);
        $this->assertEquals($taxTotal, $this->payPalOrderService->getTaxTotal());
    }

    public function testSetAndGetEnvironment(): void
    {
        $environment = PaymentGatewayEnvironment::SANDBOX;
        $this->payPalOrderService->setEnvironment($environment);
        $this->assertEquals($environment, $this->payPalOrderService->getEnvironment());
    }

    public function testSetAndGetCurrencyCode(): void
    {
        $currencyCode = 'USD';
        $this->payPalOrderService->setCurrencyCode($currencyCode);
        $this->assertEquals($currencyCode, $this->payPalOrderService->getCurrencyCode());
    }

    public function testSetAndGetValue(): void
    {
        $value = '115.50';
        $this->payPalOrderService->setValue($value);
        $this->assertEquals($value, $this->payPalOrderService->getValue());
    }

    public function testSetAndGetItems(): void
    {
        $items = [
            new Item(),
            new Item(),
        ];
        $this->payPalOrderService->setItems($items);
        $this->assertEquals($items, $this->payPalOrderService->getItems());
    }

    public function testAddItem(): void
    {
        $item = new Item();
        $this->payPalOrderService->addItem($item);
        $this->assertContains($item, $this->payPalOrderService->getItems());
    }

    public function testSetAndGetWebhookId(): void
    {
        $webhookId = 'test_webhook_id';
        $this->payPalOrderService->setWebhookId($webhookId);
        $this->assertEquals($webhookId, $this->payPalOrderService->getWebhookId());
    }

    public function testSetAndGetClientId(): void
    {
        $clientId = 'test_client_id';
        $this->payPalOrderService->setClientId($clientId);
        $this->assertEquals($clientId, $this->payPalOrderService->getClientId());
    }

    public function testSetAndGetClientSecret(): void
    {
        $clientSecret = 'test_client_secret';
        $this->payPalOrderService->setClientSecret($clientSecret);
        $this->assertEquals($clientSecret, $this->payPalOrderService->getClientSecret());
    }

    public function testGetClient(): void
    {
        $this->payPalOrderService->setClientId('test_client_id');
        $this->payPalOrderService->setClientSecret('test_client_secret');
        $this->payPalOrderService->setWebhookId('test_webhook_id');
        $this->payPalOrderService->setEnvironment(PaymentGatewayEnvironment::SANDBOX);
        $this->payPalOrderService->init();

        $this->assertInstanceOf(PaypalServerSdkClient::class, $this->payPalOrderService->getClient());
    }

    public function testInitThrowsExceptionWhenClientIdNotSet(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('PayPal client ID is not set.');

        $this->payPalOrderService->setClientSecret('test_client_secret');
        $this->payPalOrderService->setWebhookId('test_webhook_id');
        $this->payPalOrderService->setEnvironment(PaymentGatewayEnvironment::SANDBOX);

        $this->payPalOrderService->init();
    }

    public function testInitThrowsExceptionWhenClientSecretNotSet(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('PayPal client secret is not set.');

        $this->payPalOrderService->setClientId('test_client_id');
        $this->payPalOrderService->setWebhookId('test_webhook_id');
        $this->payPalOrderService->setEnvironment(PaymentGatewayEnvironment::SANDBOX);

        $this->payPalOrderService->init();
    }

    public function testInitThrowsExceptionWhenWebhookIdNotSet(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('PayPal webhook ID is not set.');

        $this->payPalOrderService->setClientId('test_client_id');
        $this->payPalOrderService->setClientSecret('test_client_secret');
        $this->payPalOrderService->setEnvironment(PaymentGatewayEnvironment::SANDBOX);

        $this->payPalOrderService->init();
    }

    public function testInitThrowsExceptionWhenEnvironmentNotSet(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('PayPal environment is not set.');

        $this->payPalOrderService->setClientId('test_client_id');
        $this->payPalOrderService->setClientSecret('test_client_secret');
        $this->payPalOrderService->setWebhookId('test_webhook_id');

        $this->payPalOrderService->init();
    }

    public function testCreateOrder(): void
    {
        $this->markTestSkipped('Cannot properly test without mocking external dependencies.');

        $this->payPalOrderService->setClientId('test_client_id');
        $this->payPalOrderService->setClientSecret('test_client_secret');
        $this->payPalOrderService->setWebhookId('test_webhook_id');
        $this->payPalOrderService->setEnvironment(PaymentGatewayEnvironment::SANDBOX);
        $this->payPalOrderService->setCurrencyCode('USD');
        $this->payPalOrderService->setValue('100.00');
        $this->payPalOrderService->setItemTotal(100.00);
        $this->payPalOrderService->setTaxTotal(10.00);
        $this->payPalOrderService->setDiscount(5.00);

        $this->payPalOrderService->init();

        $response = $this->payPalOrderService->createOrder();

        $this->assertInstanceOf(PayPalOrderResponseHandler::class, $response);
    }

    public function testGetOrder(): void
    {
        $this->markTestSkipped('Cannot properly test without mocking external dependencies.');

        $this->payPalOrderService->setClientId('test_client_id');
        $this->payPalOrderService->setClientSecret('test_client_secret');
        $this->payPalOrderService->setWebhookId('test_webhook_id');
        $this->payPalOrderService->setEnvironment(PaymentGatewayEnvironment::SANDBOX);

        $this->payPalOrderService->init();

        $orderId = 'test_order_id';

        $response = $this->payPalOrderService->getOrder($orderId);

        $this->assertInstanceOf(PayPalOrderResponseHandler::class, $response);
    }

    public function testCaptureOrder(): void
    {
        $this->markTestSkipped('Cannot properly test without mocking external dependencies.');

        $this->payPalOrderService->setClientId('test_client_id');
        $this->payPalOrderService->setClientSecret('test_client_secret');
        $this->payPalOrderService->setWebhookId('test_webhook_id');
        $this->payPalOrderService->setEnvironment(PaymentGatewayEnvironment::SANDBOX);

        $this->payPalOrderService->init();

        $orderId = 'test_order_id';

        $response = $this->payPalOrderService->captureOrder($orderId);

        $this->assertInstanceOf(PayPalOrderResponseHandler::class, $response);
    }

    protected function tearDown(): void
    {
        unset($this->payPalOrderService);
        parent::tearDown();
    }
}