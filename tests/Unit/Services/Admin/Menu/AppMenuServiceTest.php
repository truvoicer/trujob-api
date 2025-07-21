<?php

namespace Tests\Unit\Services\Admin\Menu;

use App\Models\AppMenu;
use App\Models\AppMenuItem;
use App\Services\Admin\Menu\AppMenuService;
use App\Services\ResultsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppMenuServiceTest extends TestCase
{
    use RefreshDatabase;

    private AppMenuService $appMenuService;
    private ResultsService $resultsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultsService = new ResultsService();
        $this->appMenuService = new AppMenuService($this->resultsService);
    }

    public function testAppMenuFetch(): void
    {
        $menuName = 'Test Menu';
        AppMenu::factory()->create(['name' => $menuName]);

        $appMenu = $this->appMenuService->appMenuFetch($menuName);

        $this->assertInstanceOf(AppMenu::class, $appMenu);
        $this->assertEquals($menuName, $appMenu->name);
    }

    public function testCreateAppMenu(): void
    {
        $data = ['name' => 'New Menu', 'description' => 'Description'];
        $result = $this->appMenuService->createAppMenu($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('app_menus', $data);
    }

    public function testCreateAppMenuFails(): void
    {
        $data = ['name' => null, 'description' => 'Description']; // Name is required.
        $result = $this->appMenuService->createAppMenu($data);

        $this->assertFalse($result);
        $this->assertNotEmpty($this->appMenuService->getResultsService()->getErrors());
    }

    public function testUpdateAppMenu(): void
    {
        $appMenu = AppMenu::factory()->create();
        $this->appMenuService->setAppMenu($appMenu);

        $data = ['name' => 'Updated Menu'];
        $result = $this->appMenuService->updateAppMenu($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('app_menus', ['id' => $appMenu->id, 'name' => 'Updated Menu']);
    }

    public function testUpdateAppMenuFails(): void
    {
        $appMenu = AppMenu::factory()->create();
        $this->appMenuService->setAppMenu($appMenu);

        $data = ['name' => null];
        $result = $this->appMenuService->updateAppMenu($data);

        $this->assertFalse($result);
        $this->assertNotEmpty($this->appMenuService->getResultsService()->getErrors());
    }

    public function testDeleteAppMenu(): void
    {
        $appMenu = AppMenu::factory()->create();
        $this->appMenuService->setAppMenu($appMenu);

        $result = $this->appMenuService->deleteAppMenu();

        $this->assertTrue($result);
        $this->assertDatabaseMissing('app_menus', ['id' => $appMenu->id]);
    }

    public function testDeleteAppMenuFails(): void
    {
        $appMenu = $this->getMockBuilder(AppMenu::class)
                        ->setMethods(['delete'])
                        ->getMock();

        $appMenu->method('delete')->willReturn(false);
        $this->appMenuService->setAppMenu($appMenu);
        $result = $this->appMenuService->deleteAppMenu();

        $this->assertFalse($result);
        $this->assertNotEmpty($this->appMenuService->getResultsService()->getErrors());
    }

    public function testCreateAppMenuItem(): void
    {
        $appMenu = AppMenu::factory()->create();
        $this->appMenuService->setAppMenu($appMenu);

        $data = ['label' => 'New Item', 'url' => '/new-item'];

        $result = $this->appMenuService->createAppMenuItem($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('app_menu_items', $data);
    }

    public function testCreateAppMenuItemFails(): void
    {
        $appMenu = AppMenu::factory()->create();
        $this->appMenuService->setAppMenu($appMenu);

        $data = ['label' => null, 'url' => '/new-item'];

        $result = $this->appMenuService->createAppMenuItem($data);

        $this->assertFalse($result);
        $this->assertNotEmpty($this->appMenuService->getResultsService()->getErrors());
    }


    public function testUpdateAppMenuItem(): void
    {
        $appMenuItem = AppMenuItem::factory()->create();
        $this->appMenuService->setAppMenuItem($appMenuItem);

        $data = ['label' => 'Updated Item'];
        $result = $this->appMenuService->updateAppMenuItem($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('app_menu_items', ['id' => $appMenuItem->id, 'label' => 'Updated Item']);
    }

    public function testUpdateAppMenuItemFails(): void
    {
        $appMenuItem = AppMenuItem::factory()->create();
        $this->appMenuService->setAppMenuItem($appMenuItem);

        $data = ['label' => null];
        $result = $this->appMenuService->updateAppMenuItem($data);

        $this->assertFalse($result);
        $this->assertNotEmpty($this->appMenuService->getResultsService()->getErrors());
    }

    public function testRemoveAppMenuItem(): void
    {
         $appMenu = AppMenu::factory()->create();
         $appMenuItem = AppMenuItem::factory()->create(['app_menu_id' => $appMenu->id]);
         $this->appMenuService->setAppMenu($appMenu);
         $this->appMenuService->setAppMenuItem($appMenuItem);

         $result = $this->appMenuService->removeAppMenuItem();

         $this->assertTrue($result);
         $this->assertDatabaseMissing('app_menu_items', ['id' => $appMenuItem->id]);
    }

    public function testRemoveAppMenuItemFails(): void
    {
        $appMenu = $this->getMockBuilder(AppMenu::class)
                        ->setMethods(['menuItems'])
                        ->getMock();

        $menuItemsRelation = $this->getMockBuilder(\Illuminate\Database\Eloquent\Relations\HasMany::class)
                                   ->disableOriginalConstructor()
                                   ->setMethods(['delete'])
                                   ->getMock();

        $menuItemsRelation->method('delete')->willReturn(false);

        $appMenu->method('menuItems')->willReturn($menuItemsRelation);

        $appMenuItem = AppMenuItem::factory()->create();
        $this->appMenuService->setAppMenu($appMenu);
        $this->appMenuService->setAppMenuItem($appMenuItem);

        $result = $this->appMenuService->removeAppMenuItem();

        $this->assertFalse($result);
        $this->assertNotEmpty($this->appMenuService->getResultsService()->getErrors());
    }

    public function testDeleteAppMenuItem(): void
    {
        $appMenuItem = AppMenuItem::factory()->create();
        $this->appMenuService->setAppMenuItem($appMenuItem);

        $result = $this->appMenuService->deleteAppMenuItem();

        $this->assertTrue($result);
        $this->assertDatabaseMissing('app_menu_items', ['id' => $appMenuItem->id]);
    }

    public function testDeleteAppMenuItemFails(): void
    {
        $appMenuItem = $this->getMockBuilder(AppMenuItem::class)
            ->setMethods(['delete'])
            ->getMock();

        $appMenuItem->method('delete')->willReturn(false);

        $this->appMenuService->setAppMenuItem($appMenuItem);
        $result = $this->appMenuService->deleteAppMenuItem();

        $this->assertFalse($result);
        $this->assertNotEmpty($this->appMenuService->getResultsService()->getErrors());
    }


    public function testGetResultsService(): void
    {
        $this->assertInstanceOf(ResultsService::class, $this->appMenuService->getResultsService());
    }

    public function testSetAppMenu(): void
    {
        $appMenu = new AppMenu();
        $this->appMenuService->setAppMenu($appMenu);

        $this->assertAttributeSame($appMenu, 'appMenu', $this->appMenuService);
    }

    public function testSetAppMenuItem(): void
    {
        $appMenuItem = new AppMenuItem();
        $this->appMenuService->setAppMenuItem($appMenuItem);

        $this->assertAttributeSame($appMenuItem, 'appMenuItem', $this->appMenuService);
    }
}