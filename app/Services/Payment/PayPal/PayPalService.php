<?php

namespace App\Services\Payment\PayPal;

use App\Enums\Payment\PaymentGatewayEnvironment;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Http\ApiResponse;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Item;
use PaypalServerSdkLib\Models\OAuthToken;
use PaypalServerSdkLib\PaypalServerSdkClient;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use Psr\Log\LogLevel;

class PayPalService
{
    private PaypalServerSdkClient $client;

    private string $webhookId;
    private string $clientId;
    private string $clientSecret;
    private PaymentGatewayEnvironment $environment;
    private array $items = [];
    private ?string $currencyCode = null;
    private ?string $value = null;

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


    public function __construct()
    {
    }

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

        $builder->loggingConfiguration(
            LoggingConfigurationBuilder::init()
                ->level(LogLevel::INFO)
                ->requestConfiguration(RequestLoggingConfigurationBuilder::init()->body(true))
                ->responseConfiguration(ResponseLoggingConfigurationBuilder::init()->headers(true))
        );
        $this->client = $builder->build();

        return $this;
    }

    public function createOrder(): ApiResponse
    {
        $collect = [
            'body' => OrderRequestBuilder::init(
                CheckoutPaymentIntent::CAPTURE,
                [
                    PurchaseUnitRequestBuilder::init(
                        AmountWithBreakdownBuilder::init(
                            $this->getCurrencyCode(),
                            $this->getValue() ?? '0.00'
                        )->build()
                    )->items(
                        $this->getItems()
                    )
                    // ->shipping(
                    //     $data['shipping'] ?? null
                    // )
                    ->build()
                ]
            )->build(),
            'prefer' => 'return=minimal'
        ];

        return $this->client->getOrdersController()->createOrder($collect);
    }

    public function getOrder(string $orderId): ApiResponse
    {
        return $this->client->getOrdersController()->getOrder([
            'id' => $orderId,
        ]);
    }
}
