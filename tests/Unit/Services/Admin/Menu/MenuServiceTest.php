<?php

namespace Tests\Unit\Services\Admin\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemMenu;
use App\Models\Site;
use App\Repositories\MenuRepository;
use App\Services\Admin\Menu\MenuService;
use App\Services\ResultsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuServiceTest extends TestCase
{
    use RefreshDatabase;

    private MenuService $menuService;
    private ResultsService $resultsService;
    private MenuRepository $menuRepository;
    private Site $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultsService = new ResultsService();
        $this->menuRepository = new MenuRepository();
        $this->menuService = new MenuService($this->resultsService, $this->menuRepository);

        // Create a site for testing
        $this->site = Site::factory()->create(['name' => 'Test Site']);
        $this->menuService->setSite($this->site);
    }

    public function testMenuFetch(): void
    {
        // Create a menu
        $menu = Menu::factory()->create(['name' => 'Test Menu', 'site_id' => $this->site->id]);

        // Test fetching the menu
        $fetchedMenu = $this->menuService->menuFetch('Test Menu');

        $this->assertInstanceOf(Menu::class, $fetchedMenu);
        $this->assertEquals($menu->id, $fetchedMenu->id);
    }

    public function testMoveMenuItem(): void
    {
        // Create a menu and menu item
        $menu = Menu::factory()->create(['site_id' => $this->site->id]);
        $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);

        // Mock the reorderByDirection method of the MenuRepository
        $this->menuRepository = $this->createMock(MenuRepository::class);
        $this->menuRepository->expects($this->once())
            ->method('reorderByDirection');
        $this->menuService = new MenuService($this->resultsService, $this->menuRepository);
        $this->menuService->setSite($this->site);

        // Call the moveMenuItem method
        $this->menuService->moveMenuItem($menu, $menuItem, 'up');
    }

    public function testMoveMenuItemMenu(): void
    {
        // Create a menu item and menu item menu
        $menuItem = MenuItem::factory()->create();
        $menuItemMenu = MenuItemMenu::factory()->create(['menu_item_id' => $menuItem->id]);

        // Mock the reorderByDirection method of the MenuRepository
        $this->menuRepository = $this->createMock(MenuRepository::class);
        $this->menuRepository->expects($this->once())
            ->method('reorderByDirection');

        $this->menuService = new MenuService($this->resultsService, $this->menuRepository);
        $this->menuService->setSite($this->site);

        // Call the moveMenuItemMenu method
        $this->menuService->moveMenuItemMenu($menuItem, $menuItemMenu, 'up');
    }

    public function testCreateMenu(): void
    {
        // Create menu data
        $data = ['name' => 'New Menu'];

        // Call the createMenu method
        $this->menuService->createMenu($data);

        // Assert that the menu was created
        $this->assertDatabaseHas('menus', ['name' => 'New Menu', 'site_id' => $this->site->id]);
    }

    public function testUpdateMenu(): void
    {
        // Create a menu
        $menu = Menu::factory()->create(['name' => 'Test Menu', 'site_id' => $this->site->id]);

        // Create updated menu data
        $data = ['name' => 'Updated Menu'];

        // Call the updateMenu method
        $this->menuService->updateMenu($menu, $data);

        // Assert that the menu was updated
        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'name' => 'Updated Menu']);
    }

    public function testDeleteMenu(): void
    {
        // Create a menu
        $menu = Menu::factory()->create(['name' => 'Test Menu', 'site_id' => $this->site->id]);

        // Call the deleteMenu method
        $this->menuService->deleteMenu($menu);

        // Assert that the menu was deleted
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }

    public function testCreateMenuItem(): void
    {
        // Create a menu
        $menu = Menu::factory()->create(['name' => 'Test Menu', 'site_id' => $this->site->id]);

        // Create menu item data
        $data = ['label' => 'New Menu Item', 'url' => '/new-item'];

        // Call the createMenuItem method
        $this->menuService->createMenuItem($menu, $data);

        // Assert that the menu item was created
        $this->assertDatabaseHas('menu_items', ['menu_id' => $menu->id, 'label' => 'New Menu Item', 'url' => '/new-item']);
    }

    public function testUpdateMenuItem(): void
    {
        // Create a menu and menu item
        $menu = Menu::factory()->create(['name' => 'Test Menu', 'site_id' => $this->site->id]);
        $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id, 'label' => 'Old Label']);

        // Create updated menu item data
        $data = ['label' => 'New Label'];

        // Call the updateMenuItem method
        $this->menuService->updateMenuItem($menuItem, $data);

        // Assert that the menu item was updated
        $this->assertDatabaseHas('menu_items', ['id' => $menuItem->id, 'label' => 'New Label']);
    }

    public function testMenuExistsInParents(): void
    {
        $menu = Menu::factory()->create(['site_id' => $this->site->id]);
        $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);

        $this->assertFalse($this->menuService->menuExistsInParents($menuItem, $menu));

    }

    public function testAddMenuToMenuItem(): void
    {
        $menu = Menu::factory()->create(['site_id' => $this->site->id]);
        $menuItem = MenuItem::factory()->create();

        $this->menuService->addMenuToMenuItem($menuItem, [$menu->id]);

        $this->assertDatabaseHas('menu_item_menus', ['menu_item_id' => $menuItem->id, 'menu_id' => $menu->id]);
    }

    public function testRemoveMenuItem(): void
    {
        // Create a menu and menu item
        $menu = Menu::factory()->create(['site_id' => $this->site->id]);
        $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);

        // Call the removeMenuItem method
        $this->menuService->removeMenuItem($menu, $menuItem);

        // Assert that the menu item was deleted
        $this->assertDatabaseMissing('menu_items', ['id' => $menuItem->id]);
    }

    public function testDeleteMenuItem(): void
    {
        // Create a menu item
        $menuItem = MenuItem::factory()->create();

        // Call the deleteMenuItem method
        $this->menuService->deleteMenuItem($menuItem);

        // Assert that the menu item was deleted
        $this->assertDatabaseMissing('menu_items', ['id' => $menuItem->id]);
    }

    public function testDeleteMenuItemMenu(): void
    {
        // Create a menu item menu
        $menuItemMenu = MenuItemMenu::factory()->create();

        // Call the deleteMenuItemMenu method
        $this->menuService->deleteMenuItemMenu($menuItemMenu);

        // Assert that the menu item menu was deleted
        $this->assertDatabaseMissing('menu_item_menus', ['id' => $menuItemMenu->id]);
    }

    public function testDeleteBulkMenus(): void
    {
        // Create menus
        $menu1 = Menu::factory()->create(['site_id' => $this->site->id]);
        $menu2 = Menu::factory()->create(['site_id' => $this->site->id]);

        // Call the deleteBulkMenus method
        $this->menuService->deleteBulkMenus([$menu1->id, $menu2->id]);

        // Assert that the menus were deleted
        $this->assertDatabaseMissing('menus', ['id' => $menu1->id]);
        $this->assertDatabaseMissing('menus', ['id' => $menu2->id]);
    }

    public function testGetResultsService(): void
    {
        $this->assertInstanceOf(ResultsService::class, $this->menuService->getResultsService());
    }

    public function testGetMenuRepository(): void
    {
        $this->assertInstanceOf(MenuRepository::class, $this->menuService->getMenuRepository());
    }
}