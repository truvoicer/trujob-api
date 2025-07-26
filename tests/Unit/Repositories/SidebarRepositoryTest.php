<?php

namespace Tests\Unit\Repositories;

use App\Models\Sidebar;
use App\Models\Site;
use App\Models\Widget;
use App\Repositories\SidebarRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var SidebarRepository
     */
    private $sidebarRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->sidebarRepository = new SidebarRepository();
    }


    public function test_it_can_get_the_model()
    {
        $model = $this->sidebarRepository->getModel();
        $this->assertInstanceOf(Sidebar::class, $model);
    }


    public function test_it_can_find_by_params()
    {
        $site = Site::factory()->create();
        Sidebar::factory()->count(3)->create(['site_id' => $site->id]);

        $result = $this->sidebarRepository->findByParams('id', 'asc');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Sidebar::class, $result->first());
    }


    public function test_it_can_find_by_query()
    {
        $site = Site::factory()->create();
        Sidebar::factory()->count(2)->create([
            'site_id' => $site->id,
        ]);

        $result = $this->sidebarRepository->findByQuery('some_query');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Sidebar::class, $result->first());
    }


    public function test_it_can_find_by_site()
    {
        $site = Site::factory()->create();
        $sidebar = Sidebar::factory()->create(['site_id' => $site->id]);

        $result = $this->sidebarRepository->findBySite($site);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Sidebar::class, $result->first());
        $this->assertEquals($sidebar->id, $result->first()->id);
    }


    public function test_it_can_find_sidebar_widgets()
    {
        $site = Site::factory()->create();
        $sidebar = Sidebar::factory()->create(['site_id' => $site->id]);
        $widget1 = Widget::factory()->create(['site_id' => $site->id, 'name' => 'Widget 1']);
        $widget2 = Widget::factory()->create(['site_id' => $site->id, 'name' => 'Widget 2']);
        // Create some sidebar widgets associated with the sidebar
        $sidebar->widgets()->attach([$widget1->id, $widget2->id]);

        $result = $this->sidebarRepository->findSidebarWidgets($sidebar);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);

        // Assert that the widgets are ordered correctly
        $this->assertEquals('Widget 1', $result->first()->widget->name);
        $this->assertEquals('Widget 2', $result->last()->widget->name);
    }
}
