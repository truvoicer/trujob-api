<?php

namespace Tests\Unit\Repositories;

use App\Models\Sidebar;
use App\Models\Site;
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

    
    public function it_can_get_the_model()
    {
        $model = $this->sidebarRepository->getModel();
        $this->assertInstanceOf(Sidebar::class, $model);
    }

    
    public function it_can_find_by_params()
    {
        Sidebar::factory()->count(3)->create();

        $result = $this->sidebarRepository->findByParams('id', 'asc');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Sidebar::class, $result->first());
    }

    
    public function it_can_find_by_query()
    {
        Sidebar::factory()->count(2)->create();

        $result = $this->sidebarRepository->findByQuery('some_query');

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Sidebar::class, $result->first());
    }

    
    public function it_can_find_by_site()
    {
        $site = Site::factory()->create();
        $sidebar = Sidebar::factory()->create(['site_id' => $site->id]);

        $result = $this->sidebarRepository->findBySite($site);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Sidebar::class, $result->first());
        $this->assertEquals($sidebar->id, $result->first()->id);
    }

    
    public function it_can_find_sidebar_widgets()
    {
        $sidebar = Sidebar::factory()->create();

        // Create some sidebar widgets associated with the sidebar
        $sidebar->sidebarWidgets()->create(['order' => 2, 'name' => 'Widget 2']);
        $sidebar->sidebarWidgets()->create(['order' => 1, 'name' => 'Widget 1']);

        $result = $this->sidebarRepository->findSidebarWidgets($sidebar);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);

        // Assert that the widgets are ordered correctly
        $this->assertEquals('Widget 1', $result->first()->name);
        $this->assertEquals('Widget 2', $result->last()->name);
    }
}
