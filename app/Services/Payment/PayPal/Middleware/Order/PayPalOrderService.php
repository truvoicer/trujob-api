<?php

namespace App\Services\Payment\PayPal\Middleware\Order;

use App\Enums\Payment\PaymentGatewayEnvironment;
use Money\Currency;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use PaypalServerSdkLib\Models\AmountBreakdown;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Item;
use PaypalServerSdkLib\Models\Money;
use PaypalServerSdkLib\Models\OAuthToken;
use PaypalServerSdkLib\PaypalServerSdkClient;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use Psr\Log\LogLevel;

class PayPalOrderService
{
    private PaypalServerSdkClient $client;

    private string $webhookId;
    private string $clientId;
    private string $clientSecret;
    private PaymentGatewayEnvironment $environment;
    private array $items = [];
    private ?string $currencyCode = null;
    private ?string $value = null;
    private ?float $itemTotal = null;
    private ?float $taxTotal = null;
    private ?float $discount = null;

    public function setDiscount(float $discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setItemTotal(float $itemTotal): self
    {
        $this->itemTotal = $itemTotal;
        return $this;
    }

    public function getItemTotal(): ?float
    {
        return $this->itemTotal;
    }

    public function setTaxTotal(float $taxTotal): self
    {
        $this->taxTotal = $taxTotal;
        return $this;
    }

    public function getTaxTotal(): ?float
    {
        return $this->taxTotal;
    }

    public function setEnvironment(PaymentGatewayEnvironment $environment): self
    {
        $this->environment = $environment;
        return $this;
    }
    public function getEnvironment(): PaymentGatewayEnvironment
    {
        return $this->environment;
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }
    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }
    public function getValue(): ?string
    {
        return $this->value;
    }
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    public function setWebhookId(string $webhookId): self
    {
        $this->webhookId = $webhookId;
        return $this;
    }
    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }
    public function setClientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    public function getClient(): PaypalServerSdkClient
    {
        return $this->client;
    }
    public function getWebhookId(): string
    {
        return $this->webhookId;
    }
    public function getClientId(): string
    {
        return $this->clientId;
    }
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }


    public function __construct() {}

    public function init(): self
    {
        if (!isset($this->clientId)) {
            throw new \Exception('PayPal client ID is not set.');
        }
        if (!isset($this->clientSecret)) {
            throw new \Exception('PayPal client secret is not set.');
        }
        if (!isset($this->webhookId)) {
            throw new \Exception('PayPal webhook ID is not set.');
        }
        if (!isset($this->environment)) {
            throw new \Exception('PayPal environment is not set.');
        }


        $builder = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    $this->clientId,
                    $this->clientSecret
                )
                //  ->oAuthOnTokenUpdate(
                // function (OAuthToken $oAuthToken): void {
                //     // Add the callback handler to perform operations like save to DB or file etc.
                //     // It will be triggered whenever the token gets updated.
                //     $this->saveTokenToDatabase($oAuthToken);
                // }
                // )
            );

        if ($this->environment === PaymentGatewayEnvironment::PRODUCTION) {
            $builder->environment(Environment::PRODUCTION);
        } else {
            $builder->environment(Environment::SANDBOX);
        }

        // $builder->loggingConfiguration(
        //     LoggingConfigurationBuilder::init()
        //         ->level(LogLevel::INFO)
        //         ->requestConfiguration(RequestLoggingConfigurationBuilder::init()->body(false))
        //         ->responseConfiguration(ResponseLoggingConfigurationBuilder::init()->headers(false))
        // );
        $this->client = $builder->build();

        return $this;
    }

    public function createOrder(): PayPalOrderResponseHandler
    {
        // dd([
        //     'currency_code' => $this->getCurrencyCode(),
        //     'value' => $this->getValue(),
        //     'item_total' => $this->getItemTotal(),
        //     'tax_total' => $this->getTaxTotal(),
        //     'discount' => $this->getDiscount(),
        //     'total' => $this->getItemTotal() + $this->getTaxTotal() - $this->getDiscount(),
        // ]);
        // Build the amount with breakdown
        $amountBuilder = AmountWithBreakdownBuilder::init(
            $this->getCurrencyCode(),
            $this->getValue() ?? '0.00'

        );

        $amountBreakdown = new AmountBreakdown();

        // Set item_total if available
        if ($this->getItemTotal() !== null) {
            $amountBreakdown->setItemTotal(
                new Money(
                    $this->getCurrencyCode(),
                    $this->getItemTotal() ?? 0.00
                )
            );
        }

        // Set tax_total if available
        if ($this->getTaxTotal() !== null) {
            $amountBreakdown->setTaxTotal(
                new Money(
                    $this->getCurrencyCode(),
                    $this->getTaxTotal() ?? 0.00
                )
            );
        }

        // Set discount if available
        if ($this->getDiscount() !== null) {
            $amountBreakdown->setDiscount(
                new Money(
                    $this->getCurrencyCode(),
                    $this->getDiscount() ?? 0
                )
            );
        }
        $amountBuilder->breakdown($amountBreakdown);
        $collect = [
            'body' => OrderRequestBuilder::init(
                CheckoutPaymentIntent::CAPTURE,
                [
                    PurchaseUnitRequestBuilder::init(
                        $amountBuilder->build()
                    )->items(
                        $this->getItems()
                    )
                        // ->shipping(
                        //     $data['shipping'] ?? null
                        // )
                        ->build()
                ]
            )->build()
        ];

        $response = $this->client->getOrdersController()->createOrder($collect);

        return new PayPalOrderResponseHandler($response);

    }

    public function getOrder(string $orderId): PayPalOrderResponseHandler
    {
        $response = $this->client->getOrdersController()->getOrder([
            'id' => $orderId,
        ]);
        return new PayPalOrderResponseHandler($response);
    }

    public function captureOrder(string $orderId): PayPalOrderResponseHandler
    {
        $response = $this->client->getOrdersController()->captureOrder([
            'id' => $orderId,
        ]);
        return new PayPalOrderResponseHandler($response);
    }
}
