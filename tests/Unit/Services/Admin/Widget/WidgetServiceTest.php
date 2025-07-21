<?php

namespace Tests\Unit\Services\Admin\Widget;

use App\Models\Site;
use App\Models\Widget;
use App\Repositories\WidgetRepository;
use App\Services\Admin\Widget\WidgetService;
use App\Services\ResultsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WidgetServiceTest extends TestCase
{
    use RefreshDatabase;

    private WidgetService $widgetService;
    private ResultsService $resultsService;
    private WidgetRepository $widgetRepository;
    private Site $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resultsService = $this->createMock(ResultsService::class);
        $this->widgetRepository = $this->createMock(WidgetRepository::class);
        $this->widgetService = new WidgetService($this->resultsService, $this->widgetRepository);
        $this->site = Site::factory()->create();
    }

    public function testWidgetFetch(): void
    {
        $widget = Widget::factory()->create(['site_id' => $this->site->id, 'name' => 'test-widget']);
        $fetchedWidget = $this->widgetService->widgetFetch($this->site, 'test-widget');

        $this->assertInstanceOf(Widget::class, $fetchedWidget);
        $this->assertEquals($widget->id, $fetchedWidget->id);

        $nonExistentWidget = $this->widgetService->widgetFetch($this->site, 'non-existent-widget');
        $this->assertNull($nonExistentWidget);
    }

    public function testCreateWidget(): void
    {
        $data = [
            'title' => 'Test Widget',
            'content' => 'Test Content',
        ];

        $result = $this->widgetService->createWidget($this->site, $data);
        $this->assertTrue($result);

        $this->assertDatabaseHas('widgets', [
            'site_id' => $this->site->id,
            'title' => 'Test Widget',
            'name' => 'test-widget',
            'content' => 'Test Content',
        ]);
    }

    public function testUpdateWidget(): void
    {
        $widget = Widget::factory()->create(['site_id' => $this->site->id]);

        $data = [
            'title' => 'Updated Widget Title',
            'content' => 'Updated Widget Content',
        ];

        $result = $this->widgetService->updateWidget($widget, $data);
        $this->assertTrue($result);

        $this->assertDatabaseHas('widgets', [
            'id' => $widget->id,
            'title' => 'Updated Widget Title',
            'content' => 'Updated Widget Content',
        ]);
    }

    public function testDeleteWidget(): void
    {
        $widget = Widget::factory()->create(['site_id' => $this->site->id]);

        $result = $this->widgetService->deleteWidget($widget);
        $this->assertTrue($result);

        $this->assertDatabaseMissing('widgets', [
            'id' => $widget->id,
        ]);
    }

    public function testDeleteBulkWidgets(): void
    {
        $widgets = Widget::factory()->count(2)->create(['site_id' => $this->site->id]);
        $ids = $widgets->pluck('id')->toArray();

        $result = $this->widgetService->deleteBulkWidgets($ids);
        $this->assertTrue($result);

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('widgets', ['id' => $id]);
        }

        $result = $this->widgetService->deleteBulkWidgets([]);
        $this->assertFalse($result);

    }

    public function testGetResultsService(): void
    {
        $this->assertInstanceOf(ResultsService::class, $this->widgetService->getResultsService());
    }

    public function testGetWidgetRepository(): void
    {
        $this->assertInstanceOf(WidgetRepository::class, $this->widgetService->getWidgetRepository());
    }
}