<?php

namespace Tests\Unit\Services\Block\Type;

use App\Models\PageBlock;
use App\Services\Block\Type\ProductsBlockType;
use Tests\TestCase;

class ProductsBlockTypeTest extends TestCase
{
    /**
     * @var ProductsBlockType
     */
    private $productsBlockType;

    /**
     * @var PageBlock
     */
    private $pageBlock;

    public function setUp(): void
    {
        parent::setUp();

        $this->productsBlockType = new ProductsBlockType();
        $this->pageBlock = new PageBlock();
    }

    public function tearDown(): void
    {
        unset($this->productsBlockType);
        unset($this->pageBlock);

        parent::tearDown();
    }

    public function testBuildBlockUpdateDataReturnsDataArrayUnchanged()
    {
        $data = [
            'name' => 'Test Block',
            'properties' => [
                'product_id' => 123,
            ],
        ];

        $result = $this->productsBlockType->buildBlockUpdateData($this->pageBlock, $data);

        $this->assertEquals($data, $result);
    }

    public function testBuildBlockCreateDataReturnsDataArrayUnchanged()
    {
        $data = [
            'name' => 'New Block',
            'properties' => [
                'title' => 'New Title',
            ],
        ];

        $result = $this->productsBlockType->buildBlockCreateData($this->pageBlock, $data);

        $this->assertEquals($data, $result);
    }
}