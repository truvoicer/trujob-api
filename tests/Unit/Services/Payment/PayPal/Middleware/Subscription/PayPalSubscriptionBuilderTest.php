<?php

namespace Tests\Unit;

use App\Services\Payment\PayPal\Middleware\PayPalAddressBuilder;
use App\Services\Payment\PayPal\Middleware\Subscription\PayPalSubscriptionBuilder;
use InvalidArgumentException;
use Tests\TestCase;

class PayPalSubscriptionBuilderTest extends TestCase
{
    /**
     * @var PayPalSubscriptionBuilder
     */
    protected PayPalSubscriptionBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = PayPalSubscriptionBuilder::build();
    }

    public function testSetPlanId(): void
    {
        $planId = 'P-XXXXXXXXXXXXXXX';
        $this->builder->setPlanId($planId);
        $data = $this->builder->get();
        $this->assertArrayHasKey('plan_id', $data);
        $this->assertEquals($planId, $data['plan_id']);
    }

    public function testSetStartTime(): void
    {
        $startTime = '2023-01-01T00:00:00Z';
        $this->builder->setStartTime($startTime);
        $data = $this->builder->get();
        $this->assertArrayHasKey('start_time', $data);
        $this->assertEquals($startTime, $data['start_time']);
    }

    public function testSetQuantity(): void
    {
        $quantity = 5;
        $this->builder->setQuantity($quantity);
        $data = $this->builder->get();
        $this->assertArrayHasKey('quantity', $data);
        $this->assertEquals($quantity, $data['quantity']);
    }

    public function testSetQuantityThrowsExceptionForNonPositiveValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Quantity must be a positive integer.");
        $this->builder->setQuantity(0);
    }

    public function testSetShippingAmount(): void
    {
        $currencyCode = 'USD';
        $value = '10.00';
        $this->builder->setShippingAmount($currencyCode, $value);
        $data = $this->builder->get();
        $this->assertArrayHasKey('shipping_amount', $data);
        $this->assertEquals([
            'currency_code' => $currencyCode,
            'value' => $value,
        ], $data['shipping_amount']);
    }

    public function testSetSubscriber(): void
    {
        $email = 'test@example.com';
        $nameGiven = 'John';
        $nameSurname = 'Doe';
        $addressBuilder = PayPalAddressBuilder::build()
            ->setAddressLine1('123 Main St')
            ->setAdminArea2('Anytown')
            ->setAdminArea1('CA')
            ->setPostalCode('12345')
            ->setCountryCode('US');


        $this->builder->setSubscriber($email, $nameGiven, $nameSurname, $addressBuilder);
        $data = $this->builder->get();

        $this->assertArrayHasKey('subscriber', $data);
        $this->assertEquals([
            'name' => [
                'given_name' => $nameGiven,
                'surname' => $nameSurname,
            ],
            'email_address' => $email,
        ], [
            'name' => [
                'given_name' => $data['subscriber']['name']['given_name'],
                'surname' => $data['subscriber']['name']['surname'],
                ],
                'email_address' => $data['subscriber']['email_address']
        ]);
        $this->assertArrayHasKey('shipping_address', $data['subscriber']);
    }

    public function testSetCustomId(): void
    {
        $customId = 'custom123';
        $this->builder->setCustomId($customId);
        $data = $this->builder->get();
        $this->assertArrayHasKey('custom_id', $data);
        $this->assertEquals($customId, $data['custom_id']);
    }

    public function testSetExternalId(): void
    {
        $externalId = 'external456';
        $this->builder->setExternalId($externalId);
        $data = $this->builder->get();
        $this->assertArrayHasKey('external_id', $data);
        $this->assertEquals($externalId, $data['external_id']);
    }

    public function testSetAutoRenewal(): void
    {
        $autoRenewal = true;
        $this->builder->setAutoRenewal($autoRenewal);
        $data = $this->builder->get();
        $this->assertArrayHasKey('auto_renewal', $data);
        $this->assertEquals($autoRenewal, $data['auto_renewal']);

        $autoRenewal = false;
        $this->builder->setAutoRenewal($autoRenewal);
        $data = $this->builder->get();
        $this->assertArrayHasKey('auto_renewal', $data);
        $this->assertEquals($autoRenewal, $data['auto_renewal']);
    }

    public function testGetThrowsExceptionIfPlanIdIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Subscription plan ID is required.");
        $addressBuilder = PayPalAddressBuilder::build()
            ->setAddressLine1('123 Main St')
            ->setAdminArea2('Anytown')
            ->setAdminArea1('CA')
            ->setPostalCode('12345')
            ->setCountryCode('US');

        $this->builder->setSubscriber('test@example.com', 'John', 'Doe', $addressBuilder);
        $this->builder->get();
    }

    public function testGetThrowsExceptionIfSubscriberIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Subscriber details are required.");
        $this->builder->setPlanId('P-XXXXXXXXXXXXXXX');
        $this->builder->get();
    }

    public function testGetReturnsData(): void
    {
        $planId = 'P-XXXXXXXXXXXXXXX';
        $email = 'test@example.com';
        $nameGiven = 'John';
        $nameSurname = 'Doe';

        $addressBuilder = PayPalAddressBuilder::build()
            ->setAddressLine1('123 Main St')
            ->setAdminArea2('Anytown')
            ->setAdminArea1('CA')
            ->setPostalCode('12345')
            ->setCountryCode('US');

        $this->builder->setPlanId($planId)
            ->setSubscriber($email, $nameGiven, $nameSurname, $addressBuilder);

        $data = $this->builder->get();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('plan_id', $data);
        $this->assertArrayHasKey('subscriber', $data);
        $this->assertEquals($planId, $data['plan_id']);
        $this->assertEquals($email, $data['subscriber']['email_address']);
    }
}