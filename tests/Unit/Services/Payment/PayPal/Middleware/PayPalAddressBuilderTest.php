<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware;

use App\Services\Payment\PayPal\Middleware\PayPalAddressBuilder;
use InvalidArgumentException;
use Tests\TestCase;

class PayPalAddressBuilderTest extends TestCase
{
    /**
     * @var PayPalAddressBuilder
     */
    protected PayPalAddressBuilder $addressBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->addressBuilder = PayPalAddressBuilder::build();
    }

    public function testSetAndGetAddressLine1(): void
    {
        $addressLine1 = '123 Main St';
        $this->addressBuilder->setAddressLine1($addressLine1);
        $this->assertEquals($addressLine1, $this->addressBuilder->getAddressLine1());
    }

    public function testGetAddressLine1ReturnsEmptyStringWhenNotSet(): void
    {
        $this->assertEquals('', $this->addressBuilder->getAddressLine1());
    }

    public function testSetAndGetAddressLine2(): void
    {
        $addressLine2 = 'Apt 4B';
        $this->addressBuilder->setAddressLine2($addressLine2);
        $this->assertEquals($addressLine2, $this->addressBuilder->getAddressLine2());
    }

    public function testGetAddressLine2ReturnsEmptyStringWhenNotSet(): void
    {
        $this->assertEquals('', $this->addressBuilder->getAddressLine2());
    }

    public function testSetAndGetAdminArea2(): void
    {
        $city = 'Anytown';
        $this->addressBuilder->setAdminArea2($city);
        $this->assertEquals($city, $this->addressBuilder->getAdminArea2());
    }

    public function testGetAdminArea2ReturnsEmptyStringWhenNotSet(): void
    {
        $this->assertEquals('', $this->addressBuilder->getAdminArea2());
    }

    public function testSetAndGetAdminArea1(): void
    {
        $state = 'CA';
        $this->addressBuilder->setAdminArea1($state);
        $this->assertEquals($state, $this->addressBuilder->getAdminArea1());
    }

    public function testGetAdminArea1ReturnsEmptyStringWhenNotSet(): void
    {
        $this->assertEquals('', $this->addressBuilder->getAdminArea1());
    }

    public function testSetAndGetPostalCode(): void
    {
        $postalCode = '90210';
        $this->addressBuilder->setPostalCode($postalCode);
        $this->assertEquals($postalCode, $this->addressBuilder->getPostalCode());
    }

    public function testGetPostalCodeReturnsEmptyStringWhenNotSet(): void
    {
        $this->assertEquals('', $this->addressBuilder->getPostalCode());
    }

    public function testSetAndGetCountryCode(): void
    {
        $countryCode = 'US';
        $this->addressBuilder->setCountryCode($countryCode);
        $this->assertEquals($countryCode, $this->addressBuilder->getCountryCode());
    }

    public function testGetCountryCodeReturnsEmptyStringWhenNotSet(): void
    {
        $this->assertEquals('', $this->addressBuilder->getCountryCode());
    }

    public function testSetCountryCodeThrowsExceptionForInvalidCountryCode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid country code format. Must be a valid ISO 3166-1 alpha-2 code.");
        $this->addressBuilder->setCountryCode('USA');
    }

    public function testSetCountryCodeThrowsExceptionForEmptyCountryCode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid country code format. Must be a valid ISO 3166-1 alpha-2 code.");
        $this->addressBuilder->setCountryCode('');
    }

    public function testValidateThrowsExceptionWhenAddressLine1IsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Address line 1 is required.");

        $this->addressBuilder->setAdminArea2('Anytown');
        $this->addressBuilder->setAdminArea1('CA');
        $this->addressBuilder->setPostalCode('90210');
        $this->addressBuilder->setCountryCode('US');

        $this->addressBuilder->validate();
    }

    public function testValidateThrowsExceptionWhenCityIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("City is required.");

        $this->addressBuilder->setAddressLine1('123 Main St');
        $this->addressBuilder->setAdminArea1('CA');
        $this->addressBuilder->setPostalCode('90210');
        $this->addressBuilder->setCountryCode('US');

        $this->addressBuilder->validate();
    }

    public function testValidateThrowsExceptionWhenStateIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("State is required.");

        $this->addressBuilder->setAddressLine1('123 Main St');
        $this->addressBuilder->setAdminArea2('Anytown');
        $this->addressBuilder->setPostalCode('90210');
        $this->addressBuilder->setCountryCode('US');

        $this->addressBuilder->validate();
    }

    public function testValidateThrowsExceptionWhenPostalCodeIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Postal code is required.");

        $this->addressBuilder->setAddressLine1('123 Main St');
        $this->addressBuilder->setAdminArea2('Anytown');
        $this->addressBuilder->setAdminArea1('CA');
        $this->addressBuilder->setCountryCode('US');

        $this->addressBuilder->validate();
    }

    public function testValidateThrowsExceptionWhenCountryCodeIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Country code is required.");

        $this->addressBuilder->setAddressLine1('123 Main St');
        $this->addressBuilder->setAdminArea2('Anytown');
        $this->addressBuilder->setAdminArea1('CA');
        $this->addressBuilder->setPostalCode('90210');

        $this->addressBuilder->validate();
    }

    public function testValidateDoesNotThrowExceptionWhenAllFieldsArePresent(): void
    {
        $this->addressBuilder->setAddressLine1('123 Main St');
        $this->addressBuilder->setAdminArea2('Anytown');
        $this->addressBuilder->setAdminArea1('CA');
        $this->addressBuilder->setPostalCode('90210');
        $this->addressBuilder->setCountryCode('US');

        $this->expectNotToPerformAssertions();
        $this->addressBuilder->validate();
    }
}