<?php

namespace Tests\Unit\Services\Admin\Sidebar;

use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Models\Site;
use App\Models\Widget;
use App\Repositories\SidebarRepository;
use App\Services\Admin\Sidebar\SidebarService;
use App\Services\Admin\Widget\WidgetService;
use App\Services\ResultsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class SidebarServiceTest extends TestCase
{
    use RefreshDatabase;

    private $resultsService;
    private $sidebarRepository;
    private $widgetService;
    private $sidebarService;
    private $site;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
        $this->resultsService = Mockery::mock(ResultsService::class);
        $this->sidebarRepository = Mockery::mock(SidebarRepository::class);
        $this->widgetService = Mockery::mock(WidgetService::class);

        // Instantiate the service with mocked dependencies
        $this->sidebarService = new SidebarService(
            $this->resultsService,
            $this->sidebarRepository,
            $this->widgetService
        );

        // Create a Site to use in tests
        $this->site = Site::factory()->create();
        $this->sidebarService->setSite($this->site);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSidebarFetch()
    {
        // Arrange
        $sidebarName = 'test-sidebar';
        $expectedSidebar = Sidebar::factory()->create(['name' => $sidebarName, 'site_id' => $this->site->id]);

        // Act
        $sidebar = $this->sidebarService->sidebarFetch($sidebarName);

        // Assert
        $this->assertInstanceOf(Sidebar::class, $sidebar);
        $this->assertEquals($expectedSidebar->id, $sidebar->id);
    }

    public function testMoveSidebarWidget()
    {
        // Arrange
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);
        $direction = 'up';

        $this->sidebarRepository->shouldReceive('reorderByDirection')
            ->once()
            ->with(
                $sidebarWidget,
                Mockery::type('object'), // Checking for a Eloquent\Builder instance
                $direction
            );

        // Act
        $this->sidebarService->moveSidebarWidget($sidebar, $sidebarWidget, $direction);

        // Assert (covered by Mockery expectation)
        $this->assertTrue(true); // Dummy assertion to avoid risky test
    }

    public function testCreateSidebarSuccess()
    {
        // Arrange
        $data = [
            'title' => 'Test Sidebar',
            'widgets' => [],
        ];

        $this->resultsService->shouldReceive('hasErrors')->once()->andReturn(false);

        // Act
        $result = $this->sidebarService->createSidebar($data);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('sidebars', ['title' => 'Test Sidebar', 'site_id' => $this->site->id]);
    }

    public function testCreateSidebarFailure()
    {
        // Arrange
        $data = [
            'title' => 'Test Sidebar',
            'widgets' => [],
        ];

        $this->resultsService->shouldReceive('hasErrors')->once()->andReturn(true);
        $this->resultsService->shouldReceive('addError')->once();

        // Act
        $result = $this->sidebarService->createSidebar($data);

        // Assert
        $this->assertFalse($result);
    }

    public function testUpdateSidebarSuccess()
    {
        // Arrange
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);
        $data = ['title' => 'Updated Sidebar Title'];

        // Act
        $result = $this->sidebarService->updateSidebar($sidebar, $data);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('sidebars', ['id' => $sidebar->id, 'title' => 'Updated Sidebar Title']);
    }

    public function testUpdateSidebarFailure()
    {
        // Arrange
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);
        $data = ['title' => 'Updated Sidebar Title'];

        $this->resultsService->shouldReceive('addError')->once();

        // Mock the update method to return false to simulate failure
        $sidebar->shouldReceive('update')->once()->with($data)->andReturn(false);
        $sidebar = Mockery::mock($sidebar)->makePartial(); // Use a partial mock to avoid overwriting factory

        // Act
        $result = $this->sidebarService->updateSidebar($sidebar, $data);

        // Assert
        $this->assertFalse($result);
    }

    public function testDeleteSidebarSuccess()
    {
        // Arrange
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);

        // Act
        $result = $this->sidebarService->deleteSidebar($sidebar);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('sidebars', ['id' => $sidebar->id]);
    }

    public function testDeleteSidebarFailure()
    {
        // Arrange
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);
        $this->resultsService->shouldReceive('addError')->once();

        // Mock the delete method to return false
        $sidebar->shouldReceive('delete')->once()->andReturn(false);
        $sidebar = Mockery::mock($sidebar)->makePartial();

        // Act
        $result = $this->sidebarService->deleteSidebar($sidebar);

        // Assert
        $this->assertFalse($result);
    }

    public function testCreateSidebarWidget()
    {
        // Arrange
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);
        $widget = Widget::factory()->create(['site_id' => $this->site->id]);
        $data = ['order' => 1];
        $this->sidebarRepository->shouldReceive('getHighestOrder')->once()->andReturn(1);

        // Act
        $result = $this->sidebarService->createSidebarWidget($sidebar, $widget, $data);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('sidebar_widgets', [
            'sidebar_id' => $sidebar->id,
            'widget_id' => $widget->id,
            'order' => 1,
        ]);
    }

    public function testUpdateSidebarWidgetSuccess()
    {
        // Arrange
        $sidebarWidget = SidebarWidget::factory()->create();
        $data = ['order' => 2];

        // Act
        $result = $this->sidebarService->updateSidebarWidget($sidebarWidget, $data);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('sidebar_widgets', ['id' => $sidebarWidget->id, 'order' => 2]);
    }

    public function testUpdateSidebarWidgetFailure()
    {
        // Arrange
        $sidebarWidget = SidebarWidget::factory()->create();
        $data = ['order' => 2];

        $this->resultsService->shouldReceive('addError')->once();

        // Mock the update method to return false
        $sidebarWidget->shouldReceive('update')->once()->with($data)->andReturn(false);
        $sidebarWidget = Mockery::mock($sidebarWidget)->makePartial();

        // Act
        $result = $this->sidebarService->updateSidebarWidget($sidebarWidget, $data);

        // Assert
        $this->assertFalse($result);
    }

    public function testRemoveSidebarWidgetSuccess()
    {
        // Arrange
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);

        // Mock delete on the relationship
        $sidebar->sidebarWidgets()->shouldReceive('delete')->with($sidebarWidget)->once()->andReturn(true);

        // Act
        $result = $this->sidebarService->removeSidebarWidget($sidebar, $sidebarWidget);

        // Assert
        $this->assertTrue($result);
    }

    public function testRemoveSidebarWidgetFailure()
    {
        // Arrange
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);
        $this->resultsService->shouldReceive('addError')->once();

        // Mock delete on the relationship to return false
        $sidebar->sidebarWidgets()->shouldReceive('delete')->with($sidebarWidget)->once()->andReturn(false);
        $sidebar = Mockery::mock($sidebar)->makePartial(); // Partial mock to prevent overwriting factory

        // Act
        $result = $this->sidebarService->removeSidebarWidget($sidebar, $sidebarWidget);

        // Assert
        $this->assertFalse($result);
    }

    public function testDeleteSidebarWidgetSuccess()
    {
        // Arrange
        $sidebarWidget = SidebarWidget::factory()->create();

        // Act
        $result = $this->sidebarService->deleteSidebarWidget($sidebarWidget);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('sidebar_widgets', ['id' => $sidebarWidget->id]);
    }

    public function testDeleteSidebarWidgetFailure()
    {
        // Arrange
        $sidebarWidget = SidebarWidget::factory()->create();
        $this->resultsService->shouldReceive('addError')->once();

        // Mock delete method to return false
        $sidebarWidget->shouldReceive('delete')->once()->andReturn(false);
        $sidebarWidget = Mockery::mock($sidebarWidget)->makePartial();

        // Act
        $result = $this->sidebarService->deleteSidebarWidget($sidebarWidget);

        // Assert
        $this->assertFalse($result);
    }

    public function testDeleteBulkSidebarsSuccess()
    {
        // Arrange
        $sidebars = Sidebar::factory()->count(2)->create(['site_id' => $this->site->id]);
        $ids = $sidebars->pluck('id')->toArray();

        // Mock deleteSidebar to always return true
        $this->sidebarService->shouldReceive('deleteSidebar')
            ->twice()
            ->andReturn(true);

        // Act
        $result = $this->sidebarService->deleteBulkSidebars($ids);

        // Assert
        $this->assertTrue($result);
    }

    public function testDeleteBulkSidebarsEmptyIds()
    {
        // Arrange
        $ids = [];

        // Act
        $result = $this->sidebarService->deleteBulkSidebars($ids);

        // Assert
        $this->assertFalse($result);
    }

     /**
     * @throws \Exception
     */
    public function testDeleteBulkSidebarsFailure()
    {
        // Arrange
        $this->expectException(\Exception::class);

        $sidebars = Sidebar::factory()->count(2)->create(['site_id' => $this->site->id]);
        $ids = $sidebars->pluck('id')->toArray();

        // Mock deleteSidebar to return false on first call
        $this->sidebarService->shouldReceive('deleteSidebar')
            ->once()
            ->andReturn(false);
        $this->sidebarService->shouldReceive('deleteSidebar')
            ->andReturn(true); // Avoid infinite loop

        // Act
        $this->sidebarService->deleteBulkSidebars($ids);
    }

    public function testGetResultsService()
    {
        // Act
        $result = $this->sidebarService->getResultsService();

        // Assert
        $this->assertInstanceOf(ResultsService::class, $result);
        $this->assertSame($this->resultsService, $result);
    }

    public function testGetSidebarRepository()
    {
        // Act
        $result = $this->sidebarService->getSidebarRepository();

        // Assert
        $this->assertInstanceOf(SidebarRepository::class, $result);
        $this->assertSame($this->sidebarRepository, $result);
    }
}