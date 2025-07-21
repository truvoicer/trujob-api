<?php

namespace Tests\Unit\Services\Block;

use App\Enums\Block\BlockType;
use App\Models\Block;
use App\Services\Block\BlockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var BlockService
     */
    private $blockService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->blockService = new BlockService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->blockService);
    }

    /**
     * Test that defaultBlockTypes() creates or updates Block records for each BlockType enum value.
     *
     * @return void
     */
    public function testDefaultBlockTypesCreatesOrUpdatesBlocks()
    {
        // Arrange:  We start with no blocks in the database.
        $this->assertEmpty(Block::all());

        // Act:  Call the method under test.
        $this->blockService->defaultBlockTypes();

        // Assert:  We now have Block records for each BlockType enum value.
        $blockTypes = BlockType::cases();
        $this->assertCount(count($blockTypes), Block::all());

        foreach ($blockTypes as $blockType) {
            $this->assertDatabaseHas('blocks', ['type' => $blockType->value]);
        }

        // Act: Call the method again to ensure it updates and doesn't duplicate.
        $this->blockService->defaultBlockTypes();

        // Assert: The count should still be the same, indicating update, not create.
        $this->assertCount(count($blockTypes), Block::all());
    }
}
