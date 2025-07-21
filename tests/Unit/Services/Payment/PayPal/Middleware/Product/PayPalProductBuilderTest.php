<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware\Product;

use App\Services\Payment\PayPal\Middleware\Product\PayPalProductBuilder;
use InvalidArgumentException;
use Tests\TestCase;

class PayPalProductBuilderTest extends TestCase
{
    /**
     * @var PayPalProductBuilder
     */
    protected PayPalProductBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = PayPalProductBuilder::build();
    }

    public function testSetName(): void
    {
        $name = 'Test Product';
        $this->builder->setName($name);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($name, $data['name']);
    }

    public function testSetId(): void
    {
        $id = 'product123';
        $this->builder->setId($id);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($id, $data['id']);
    }

    public function testSetTypeValid(): void
    {
        $type = 'DIGITAL';
        $this->builder->setType($type);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($type, $data['type']);
    }

    public function testSetTypeInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->builder->setType('INVALID');
    }

    public function testSetCategory(): void
    {
        $category = 'Software';
        $this->builder->setCategory($category);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($category, $data['category']);
    }

    public function testSetDescription(): void
    {
        $description = 'A test product description.';
        $this->builder->setDescription($description);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($description, $data['description']);
    }

    public function testSetImageUrl(): void
    {
        $imageUrl = 'http://example.com/image.jpg';
        $this->builder->setImageUrl($imageUrl);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($imageUrl, $data['image_url']);
    }

    public function testSetHomeUrl(): void
    {
        $homeUrl = 'http://example.com';
        $this->builder->setHomeUrl($homeUrl);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($homeUrl, $data['home_url']);
    }

    public function testSetTaxCategory(): void
    {
        $taxCategory = 'Test Tax Category';
        $this->builder->setTaxCategory($taxCategory);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($taxCategory, $data['tax_category']);
    }

    public function testSetRepresentation(): void
    {
        $representation = 'DIGITAL_GOODS';
        $this->builder->setRepresentation($representation);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($representation, $data['representation']);
    }

    public function testSetUsageType(): void
    {
        $usageType = 'MERCHANT_INITIATED_BILLING';
        $this->builder->setUsageType($usageType);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($usageType, $data['usage_type']);
    }

    public function testSetStatus(): void
    {
        $status = 'ACTIVE';
        $this->builder->setStatus($status);
        $data = $this->builder->get(); //Need to call get to build the full array
        $this->assertEquals($status, $data['status']);
    }

    public function testGetValidData(): void
    {
        $name = 'Test Product';
        $type = 'DIGITAL';
        $this->builder->setName($name)->setType($type);

        $data = $this->builder->get();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('type', $data);
        $this->assertEquals($name, $data['name']);
        $this->assertEquals($type, $data['type']);
    }

    public function testGetNameMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Product name is required.");
        $this->builder->setType('DIGITAL')->get();
    }

    public function testGetTypeMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Product type is required.");
        $this->builder->setName('Test Product')->get();
    }

    protected function tearDown(): void
    {
        unset($this->builder);
        parent::tearDown();
    }
}