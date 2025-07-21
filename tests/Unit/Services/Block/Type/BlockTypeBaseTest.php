<?php

namespace Tests\Unit\Services\Block\Type;

use App\Models\PageBlock;
use App\Services\Block\Type\BlockTypeBase;
use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

class BlockTypeBaseTest extends TestCase
{
    /**
     * @var MockInterface|BlockTypeBase
     */
    private $blockTypeBase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blockTypeBase = Mockery::mock(BlockTypeBase::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testBuildBlockUpdateData(): void
    {
        $pageBlock = new PageBlock();
        $data = ['some' => 'data'];
        $expected = ['updated' => 'data'];

        $this->blockTypeBase->shouldReceive('buildBlockUpdateData')
            ->with($pageBlock, $data)
            ->andReturn($expected);

        $actual = $this->blockTypeBase->buildBlockUpdateData($pageBlock, $data);

        $this->assertEquals($expected, $actual);
    }

    public function testBuildBlockCreateData(): void
    {
        $pageBlock = new PageBlock();
        $data = ['some' => 'data'];
        $expected = ['created' => 'data'];

        $this->blockTypeBase->shouldReceive('buildBlockCreateData')
            ->with($pageBlock, $data)
            ->andReturn($expected);

        $actual = $this->blockTypeBase->buildBlockCreateData($pageBlock, $data);

        $this->assertEquals($expected, $actual);
    }
}