<?php

namespace Tests\Unit\Models;

use App\Models\MenuMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuMenuItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var MenuMenuItem
     */
    private $menuMenuItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->menuMenuItem = new MenuMenuItem();
    }

    protected function tearDown(): void
    {
        unset($this->menuMenuItem);

        parent::tearDown();
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(MenuMenuItem::class, $this->menuMenuItem);
    }

    /** @test */
    public function it_has_a_table_name()
    {
        $this->assertEquals('menu_menu_items', $this->menuMenuItem->getTable());
    }

    /** @test */
    public function it_can_create_a_menu_menu_item()
    {
        $menuMenuItem = MenuMenuItem::create([
            // Add necessary attributes for your model, for now let's use a dummy one.
            'name' => 'Test Menu Item',
        ]);

        $this->assertInstanceOf(MenuMenuItem::class, $menuMenuItem);
        $this->assertDatabaseHas('menu_menu_items', ['name' => 'Test Menu Item']);
    }
}
