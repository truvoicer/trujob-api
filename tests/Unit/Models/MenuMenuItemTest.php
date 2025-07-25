<?php

namespace Tests\Unit\Models;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuMenuItem;
use App\Models\Site;
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


    public function test_it_can_be_instantiated()
    {
        $this->assertInstanceOf(MenuMenuItem::class, $this->menuMenuItem);
    }


    public function test_it_has_a_table_name()
    {
        $this->assertEquals('menu_menu_items', $this->menuMenuItem->getTable());
    }


    public function test_it_can_create_a_menu_menu_item()
    {

        $site = Site::factory()->create();
        $menu = Menu::factory()->create([
            'site_id' => $site->id
        ]);
        $menuItem = MenuItem::factory()->create([
        ]);
        $menuMenuItem = MenuMenuItem::create([
            'menu_id' => $menu->id,
            'menu_item_id' => $menuItem->id,
            'order' => 1,
            'active' => true
        ]);

        $this->assertInstanceOf(MenuMenuItem::class, $menuMenuItem);
        $this->assertDatabaseHas('menu_menu_items', [
            'menu_id' => $menu->id,
            'menu_item_id' => $menuItem->id,
            'order' => 1,
            'active' => true
        ]);
    }
}
