<?php

namespace Tests\Unit\Services\Payment\Stripe\Middleware\Checkout;

use App\Services\Payment\Stripe\Middleware\Checkout\StripeSubscriptionDataBuilder;
use Tests\TestCase;

class StripeSubscriptionDataBuilderTest extends TestCase
{
    /**
     * @var StripeSubscriptionDataBuilder
     */
    protected $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new StripeSubscriptionDataBuilder();
    }

    protected function tearDown(): void
    {
        unset($this->builder);
        parent::tearDown();
    }

    public function testMake()
    {
        $builder = StripeSubscriptionDataBuilder::make();
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $builder);
    }

    public function testSetTrialPeriodDays()
    {
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setTrialPeriodDays(30));
        $params = $this->builder->build();
        $this->assertArrayHasKey('trial_period_days', $params);
        $this->assertEquals(30, $params['trial_period_days']);
    }

    public function testSetTrialEnd()
    {
        $timestamp = time() + (30 * 24 * 60 * 60); // 30 days from now
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setTrialEnd($timestamp));
        $params = $this->builder->build();
        $this->assertArrayHasKey('trial_end', $params);
        $this->assertEquals($timestamp, $params['trial_end']);
    }

    public function testSetDefaultPaymentMethod()
    {
        $paymentMethodId = 'pm_1234567890';
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setDefaultPaymentMethod($paymentMethodId));
        $params = $this->builder->build();
        $this->assertArrayHasKey('default_payment_method', $params);
        $this->assertEquals($paymentMethodId, $params['default_payment_method']);
    }

    public function testSetDefaultSource()
    {
        $sourceId = 'src_1234567890';
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setDefaultSource($sourceId));
        $params = $this->builder->build();
        $this->assertArrayHasKey('default_source', $params);
        $this->assertEquals($sourceId, $params['default_source']);
    }

    public function testSetDefaultTaxRates()
    {
        $taxRateIds = ['txr_123', 'txr_456'];
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setDefaultTaxRates($taxRateIds));
        $params = $this->builder->build();
        $this->assertArrayHasKey('default_tax_rates', $params);
        $this->assertEquals($taxRateIds, $params['default_tax_rates']);
    }

    public function testSetCoupon()
    {
        $couponId = 'coupon_12345';
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setCoupon($couponId));
        $params = $this->builder->build();
        $this->assertArrayHasKey('coupon', $params);
        $this->assertEquals($couponId, $params['coupon']);
    }

    public function testSetPromotionCode()
    {
        $promotionCodeId = 'promo_12345';
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setPromotionCode($promotionCodeId));
        $params = $this->builder->build();
        $this->assertArrayHasKey('promotion_code', $params);
        $this->assertEquals($promotionCodeId, $params['promotion_code']);
    }

    public function testSetMetadata()
    {
        $metadata = ['key1' => 'value1', 'key2' => 'value2'];
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setMetadata($metadata));
        $params = $this->builder->build();
        $this->assertArrayHasKey('metadata', $params);
        $this->assertEquals($metadata, $params['metadata']);

        $metadata2 = ['key3' => 'value3'];
        $this->builder->setMetadata($metadata2);
        $params = $this->builder->build();
        $expectedMetadata = array_merge($metadata, $metadata2);
        $this->assertEquals($expectedMetadata, $params['metadata']);
    }

    public function testSetApplicationFeePercent()
    {
        $percent = 2.5;
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setApplicationFeePercent($percent));
        $params = $this->builder->build();
        $this->assertArrayHasKey('application_fee_percent', $params);
        $this->assertEquals($percent, $params['application_fee_percent']);
    }

    public function testSetBillingCycleAnchor()
    {
        $timestamp = time() + (60 * 60 * 24); // 1 day from now
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setBillingCycleAnchor($timestamp));
        $params = $this->builder->build();
        $this->assertArrayHasKey('billing_cycle_anchor', $params);
        $this->assertEquals($timestamp, $params['billing_cycle_anchor']);
    }

    public function testSetBillingMode()
    {
        $type = 'flexible';
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setBillingMode($type));
        $params = $this->builder->build();
        $this->assertArrayHasKey('billing_mode', $params);
        $this->assertEquals(['type' => $type], $params['billing_mode']);
    }

    public function testSetDescription()
    {
        $description = 'Test Subscription Description';
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setDescription($description));
        $params = $this->builder->build();
        $this->assertArrayHasKey('description', $params);
        $this->assertEquals($description, $params['description']);
    }

    public function testSetInvoiceSettings()
    {
        $settings = ['footer' => 'Test Footer'];
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setInvoiceSettings($settings));
        $params = $this->builder->build();
        $this->assertArrayHasKey('invoice_settings', $params);
        $this->assertEquals($settings, $params['invoice_settings']);

        $settings2 = ['rendering_options' => ['amount_tax_display' => 'include_inclusive_tax']];
        $this->builder->setInvoiceSettings($settings2);
        $params = $this->builder->build();
        $expectedSettings = array_merge($settings, $settings2);
        $this->assertEquals($expectedSettings, $params['invoice_settings']);
    }

    public function testSetOnBehalfOf()
    {
        $accountId = 'acct_12345';
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setOnBehalfOf($accountId));
        $params = $this->builder->build();
        $this->assertArrayHasKey('on_behalf_of', $params);
        $this->assertEquals($accountId, $params['on_behalf_of']);
    }

    public function testSetProrationBehavior()
    {
        $behavior = 'create_prorations';
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setProrationBehavior($behavior));
        $params = $this->builder->build();
        $this->assertArrayHasKey('proration_behavior', $params);
        $this->assertEquals($behavior, $params['proration_behavior']);
    }

    public function testSetTransferData()
    {
        $transferData = ['destination' => 'acct_12345'];
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setTransferData($transferData));
        $params = $this->builder->build();
        $this->assertArrayHasKey('transfer_data', $params);
        $this->assertEquals($transferData, $params['transfer_data']);

        $transferData2 = ['amount_percent' => 10.0];
        $this->builder->setTransferData($transferData2);
        $params = $this->builder->build();
        $expectedTransferData = array_merge($transferData, $transferData2);
        $this->assertEquals($expectedTransferData, $params['transfer_data']);

    }

    public function testSetTrialSettings()
    {
        $trialSettings = ['end_behavior' => ['missing_payment_method' => 'cancel']];
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->setTrialSettings($trialSettings));
        $params = $this->builder->build();
        $this->assertArrayHasKey('trial_settings', $params);
        $this->assertEquals($trialSettings, $params['trial_settings']);

        $trialSettings2 = ['reset_clock_on_trial_end' => true];
        $this->builder->setTrialSettings($trialSettings2);
        $params = $this->builder->build();
        $expectedTrialSettings = array_merge($trialSettings, $trialSettings2);
        $this->assertEquals($expectedTrialSettings, $params['trial_settings']);

    }

    public function testAddParameter()
    {
        $key = 'custom_key';
        $value = 'custom_value';
        $this->assertInstanceOf(StripeSubscriptionDataBuilder::class, $this->builder->addParameter($key, $value));
        $params = $this->builder->build();
        $this->assertArrayHasKey($key, $params);
        $this->assertEquals($value, $params[$key]);
    }

    public function testBuild()
    {
        $this->builder->setTrialPeriodDays(15);
        $params = $this->builder->build();
        $this->assertIsArray($params);
        $this->assertArrayHasKey('trial_period_days', $params);
        $this->assertEquals(15, $params['trial_period_days']);

        $builder = new StripeSubscriptionDataBuilder();
        $params = $builder->build();
        $this->assertIsArray($params);
        $this->assertEmpty($params);
    }
}