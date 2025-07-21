<?php

namespace Tests\Unit\Repositories;

use App\Models\Widget;
use App\Models\Site;
use App\Repositories\WidgetRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WidgetRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private WidgetRepository $widgetRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->widgetRepository = new WidgetRepository();
    }

    public function testGetModel(): void
    {
        $model = $this->widgetRepository->getModel();
        $this->assertInstanceOf(Widget::class, $model);
    }

    public function testFindByParams(): void
    {
        // Arrange
        Widget::factory()->count(3)->create();
        $sort = 'name';
        $order = 'asc';
        $count = 2;

        // Act
        $result = $this->widgetRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount($count, $result);

        // Test with null count
        $result = $this->widgetRepository->findByParams($sort, $order);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function testFindByQuery(): void
    {
        // Arrange
        Widget::factory()->count(2)->create();

        // Act
        $result = $this->widgetRepository->findByQuery('test');

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function testFindBySite(): void
    {
        // Arrange
        $site = Site::factory()->create();
        $widgets = Widget::factory()->count(2)->create(['site_id' => $site->id]);

        // Act
        $result = $this->widgetRepository->findBySite($site);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals($widgets->first()->id, $result->first()->id);
    }

}
